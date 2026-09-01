<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\UserBook;
use App\Models\AdminRecommendation;
use App\Models\friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    private string $googleApiKey = 'AIzaSyBGjDodZWAvBQ57QjOZ24VAGHOKf2p0Pus';

    /**
     * Kapağı Cloudinary API'sine doğrudan yükleyip kalıcı CDN URL'sini döner
     */
    private function getCachedCoverUrl(?string $url, string $key): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (str_contains($url, 'res.cloudinary.com')) {
            return $url;
        }

        $cloudName = env('CLOUDINARY_CLOUD_NAME', 'fxyz37re');
        $apiKey    = env('CLOUDINARY_API_KEY', '899324875961436');
        $apiSecret = env('CLOUDINARY_API_SECRET', '8z8E_0aF6fG7Q68bCq48b9v2NqI');

        $timestamp = time();
        $folder    = 'bookie_covers';
        $publicId  = 'cover_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $key);

        $paramsToSign = "folder={$folder}&public_id={$publicId}&timestamp={$timestamp}";
        $signature    = sha1($paramsToSign . $apiSecret);

        try {
            $response = Http::withoutVerifying()->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                'file'      => $url,
                'api_key'   => $apiKey,
                'timestamp' => $timestamp,
                'folder'    => $folder,
                'public_id' => $publicId,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                return $response->json('secure_url');
            }
        } catch (\Throwable $e) {
            Log::warning('Cloudinary kapak yükleme hatası: ' . $e->getMessage());
        }

        return $url;
    }

    public function index()
    {
        $currentWeek = now()->format('Y_W');
        $cacheKey = 'popular_books_' . $currentWeek;

        $popularBooks = Cache::remember($cacheKey, 604800, function () use ($currentWeek) {
            $savedBooks = [];

            try {
                $weekNumber = (int) now()->format('W');
                $offset = ($weekNumber * 6) % 60;

                $response = Http::withoutVerifying()
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                    ->timeout(8)
                    ->get('https://openlibrary.org/search.json', [
                        'q'      => 'subject:classic_literature',
                        'limit'  => 30,
                        'offset' => $offset,
                    ]);

                if ($response->successful()) {
                    $docs = $response->json('docs') ?? [];

                    foreach ($docs as $doc) {
                        $title = $doc['title'] ?? '';
                        
                        if (
                            empty($title) || 
                            empty($doc['cover_i']) || 
                            str_contains(strtolower($title), 'gutenberg') || 
                            preg_match('/[^\p{Latin}\p{N}\s\p{P}]/u', $title)
                        ) {
                            continue;
                        }

                        $rawKey = $doc['key'] ?? '';
                        $cleanId = str_replace(['/works/', '/books/'], '', $rawKey);
                        $olKey = 'OL_' . $cleanId;
                        $coverUrl = "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-M.jpg";
                        $author = $doc['author_name'][0] ?? 'Bilinmeyen Yazar';

                        $book = Book::updateOrCreate(
                            ['open_library_key' => $olKey],
                            [
                                'title'       => $title,
                                'page_count'  => $doc['number_of_pages_median'] ?? ($doc['number_of_pages'] ?? null),
                                'author'      => $author,
                                'cover_image' => $this->getCachedCoverUrl($coverUrl, $olKey),
                            ]
                        );

                        $savedBooks[] = [
                            'id'     => $book->open_library_key,
                            'title'  => $book->title,
                            'author' => $book->author,
                            'cover'  => $book->cover_image,
                        ];

                        if (count($savedBooks) >= 6) break;
                    }

                    if (count($savedBooks) >= 6) {
                        return $savedBooks;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Haftalık popüler kitaplar çekilemedi: ' . $e->getMessage());
            }

            return Book::whereNotNull('cover_image')->latest('id')->take(6)->get()->map(function ($b) {
                return [
                    'id'     => $b->open_library_key ?? $b->google_book_id ?? (string)$b->id,
                    'title'  => $b->title,
                    'author' => $b->author,
                    'cover'  => $b->cover_image,
                ];
            })->toArray();
        });

        $pendingRequests = friendship::with('sender')
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->get();

        $adminRecommendation = AdminRecommendation::latest()->first();

        return view('dashboard', [
            'popularBooks'        => $popularBooks ?? [],
            'adminRecommendation' => $adminRecommendation,
            'pendingRequests'     => $pendingRequests,
        ]);
    }

    public function show($key)
    {
        $cleanKey = trim((string)$key);
        $pageCount = null;

        $book = Book::where('id', is_numeric($cleanKey) ? (int)$cleanKey : 0)
            ->orWhere('open_library_key', $cleanKey)
            ->orWhere('google_book_id', $cleanKey)
            ->orWhere('open_library_key', 'OL_' . $cleanKey)
            ->orWhere('open_library_key', str_replace('OL_', '', $cleanKey))
            ->first();

        $userBook = null;
        $allReviews = collect();

        if ($book) {
            $title = $book->title;
            $authors = $book->author;
            $pageCount = $book->page_count;

            $rawCover = $book->cover_image;
            if (!empty($rawCover) && !str_contains($rawCover, 'res.cloudinary.com')) {
                $cachedPath = $this->getCachedCoverUrl($rawCover, $cleanKey);
                if ($cachedPath !== $rawCover) {
                    $book->update(['cover_image' => $cachedPath]);
                    $rawCover = $cachedPath;
                }
            }

            $coverUrl = $rawCover ?: null;
            $description = $book->description ?? 'No description available.';

            if (auth()->check()) {
                $userBook = UserBook::where('user_id', auth()->id())
                    ->where('book_id', $book->id)
                    ->first();
            }

            $allReviews = UserBook::with(['user', 'likes'])
                ->where('book_id', $book->id)
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('review')->where('review', '!=', '');
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('rating')->where('rating', '>', 0);
                    });
                })
                ->latest()
                ->get();
        } else {
            $cacheKey = "book_meta_{$cleanKey}";
            $cached = Cache::get($cacheKey);

            $title = $cached['title'] ?? 'Unknown book';
            $authors = $cached['authors'] ?? 'Unknown author';
            $coverUrl = $cached['coverUrl'] ?? null;
            $description = $cached['description'] ?? 'No description available.';
            $pageCount = $cached['pageCount'] ?? null;
        }

        $ratedReviews = $allReviews->where('rating', '>', 0);
        $averageRating = $ratedReviews->isNotEmpty() ? round($ratedReviews->avg('rating'), 1) : 0;
        $totalReviews = $ratedReviews->count();

        return view('show', compact(
            'book',
            'userBook',
            'allReviews',
            'title',
            'authors',
            'coverUrl',
            'description',
            'averageRating',
            'totalReviews',
            'pageCount'
        ));
    }

    public function searchApi(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'search_safe_' . md5(mb_strtolower($query, 'UTF-8'));
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (!empty($cached)) {
                return response()->json($cached);
            }
        }

        // 1. Yerel Veritabanı
        $localBooks = Book::where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('author', 'LIKE', "%{$query}%");
            })
            ->whereNotNull('cover_image')
            ->limit(5)
            ->get()
            ->map(function ($b) {
                return [
                    'id'      => $b->google_book_id ?? ($b->open_library_key ?? (string)$b->id),
                    'title'   => $b->title,
                    'authors' => $b->author,
                    'cover'   => $b->cover_image,
                ];
            })->toArray();

        // 2. Dış API'ler
        $googleResults = $this->searchGoogleBooks($query);
        $openLibResults = $this->searchOpenLibrary($query);

        $merged = array_merge($localBooks, $googleResults, $openLibResults);
        
        $cleanQuery = mb_strtolower($query, 'UTF-8');
        $stopwords = ['ve', 'ile', 'de', 'da', 'bir', 'the', 'and', 'of', 'in', 'a', 'an'];
        $queryWords = array_filter(explode(' ', $cleanQuery), function($w) use ($stopwords) {
            return mb_strlen($w, 'UTF-8') > 1 && !in_array($w, $stopwords);
        });

        $filteredResults = [];
        $seenKeys = [];

        foreach ($merged as $item) {
            $titleLower = mb_strtolower($item['title'], 'UTF-8');
            $authorLower = mb_strtolower($item['authors'] ?? '', 'UTF-8');

            $isRelevant = false;
            foreach ($queryWords as $word) {
                if (str_contains($titleLower, $word) || str_contains($authorLower, $word)) {
                    $isRelevant = true;
                    break;
                }
            }

            if (!$isRelevant) {
                continue;
            }

            $uniqueKey = $titleLower . '_' . mb_substr($authorLower, 0, 8);
            if (!isset($seenKeys[$uniqueKey])) {
                $seenKeys[$uniqueKey] = true;
                $filteredResults[] = $item;
            }
        }

        // Orijinal Sıralama Mantığı
        usort($filteredResults, function ($a, $b) use ($cleanQuery, $queryWords) {
            $tA = mb_strtolower($a['title'], 'UTF-8');
            $tB = mb_strtolower($b['title'], 'UTF-8');

            if ($tA === $cleanQuery && $tB !== $cleanQuery) return -1;
            if ($tA !== $cleanQuery && $tB === $cleanQuery) return 1;

            $startsA = str_starts_with($tA, $cleanQuery);
            $startsB = str_starts_with($tB, $cleanQuery);
            if ($startsA && !$startsB) return -1;
            if (!$startsA && $startsB) return 1;

            $scoreA = 0;
            $scoreB = 0;
            foreach ($queryWords as $word) {
                if (str_contains($tA, $word)) $scoreA++;
                if (str_contains($tB, $word)) $scoreB++;
            }

            if ($scoreA !== $scoreB) {
                return $scoreB <=> $scoreA;
            }

            return 0;
        });

        $results = array_slice($filteredResults, 0, 10);

        if (!empty($results)) {
            foreach ($results as $book) {
                Cache::put('book_meta_' . $book['id'], [
                    'title'       => $book['title'],
                    'authors'     => $book['authors'],
                    'coverUrl'    => $book['cover'],
                    'description' => 'Açıklama yükleniyor...',
                ], 604800);
            }
            Cache::put($cacheKey, $results, now()->addHours(24));
        }

        return response()->json($results);
    }

    private function searchGoogleBooks(string $query): array
    {
        try {
            $params = [
                'q'          => 'intitle:' . $query,
                'maxResults' => 15,
                'printType'  => 'books',
            ];

            if (!empty($this->googleApiKey)) {
                $params['key'] = $this->googleApiKey;
            }

            $response = Http::withoutVerifying()
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->timeout(4)
                ->get('https://www.googleapis.com/books/v1/volumes', $params);

            if (!$response->successful()) {
                return [];
            }

            $items = $response->json('items') ?? [];
            $results = [];

            foreach ($items as $item) {
                $info = $item['volumeInfo'] ?? [];
                
                $rawCover = $info['imageLinks']['thumbnail'] ?? ($info['imageLinks']['smallThumbnail'] ?? null);
                if (empty($rawCover)) {
                    continue;
                }

                $cover = str_replace(['http://', '&edge=curl'], ['https://', ''], $rawCover);
                $title = $info['title'] ?? null;
                if (!$title) continue;

                $authors = isset($info['authors']) ? implode(', ', array_slice($info['authors'], 0, 2)) : 'Bilinmeyen Yazar';

                $results[] = [
                    'id'      => $item['id'],
                    'title'   => $title,
                    'authors' => $authors,
                    'cover'   => $cover,
                ];
            }

            return $results;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function searchOpenLibrary(string $query): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) BookieApp/1.0',
                    'Accept'     => 'application/json',
                ])
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->timeout(4)
                ->get('https://openlibrary.org/search.json', [
                    'title' => $query,
                    'limit' => 20,
                ]);

            if (!$response->successful()) {
                return [];
            }

            $docs = $response->json('docs') ?? [];
            $results = [];

            foreach ($docs as $doc) {
                $coverId = $doc['cover_i'] ?? null;
                if (empty($coverId)) {
                    continue;
                }

                $title = $doc['title'] ?? null;
                if (!$title) continue;

                $rawKey = $doc['key'] ?? '';
                $cleanId = str_replace(['/works/', '/books/'], '', $rawKey);
                $bookId = 'OL_' . $cleanId;

                $authors = isset($doc['author_name']) ? implode(', ', array_slice($doc['author_name'], 0, 2)) : 'Bilinmeyen Yazar';
                $cover = "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg";

                $results[] = [
                    'id'      => $bookId,
                    'title'   => $title,
                    'authors' => $authors,
                    'cover'   => $cover,
                ];
            }

            return $results;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|string',
            'status' => 'required',
            'rating' => 'nullable|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rawKey = request()->route('key') ?? $request->input('open_library_key') ?? $request->input('google_book_id');
        $cleanKey = trim((string)$rawKey);

        $coverValue = $request->input('cover_image') ?? $request->input('cover_url');
        if (!empty($coverValue)) {
            $coverValue = $this->getCachedCoverUrl($coverValue, $cleanKey);
        }

        $book = Book::where('open_library_key', $cleanKey)
            ->orWhere('google_book_id', $cleanKey)
            ->orWhere('id', is_numeric($cleanKey) ? (int)$cleanKey : 0)
            ->first();

        if (!$book) {
            $book = Book::create([
                'user_id'          => auth()->id(),
                'google_book_id'   => $cleanKey,
                'open_library_key' => $cleanKey,
                'title'            => $request->input('title'),
                'author'           => $request->input('author') ?? 'Unknown Author',
                'cover_image'      => $coverValue,
            ]);
        } else {
            if (empty($book->cover_image) && !empty($coverValue)) {
                $book->update(['cover_image' => $coverValue]);
            }
        }

        $status = $request->input('status');
        if ($status === 'want_to_read' || $status === 'to_read') {
            $status = 'toRead';
        }

        UserBook::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $book->id,
            ],
            [
                'status' => $status,
                'rating' => $request->filled('rating') ? (int)$request->rating : null,
                'review' => $request->filled('review') ? $request->review : null,
            ]
        );

        return back()->with('success', 'Kitap kütüphanene kaydedildi!');
    }

    public function saveOrUpdate(Request $request, $key)
    {
        return $this->store($request);
    }

    public function getRandomRecommendation(Request $request)
    {
        $genre = $request->query('genre', 'random');

        $genreQueries = [
            'fantasy'         => 'fantasy',
            'classic'         => 'classics',
            'romance'         => 'romance',
            'science_fiction' => 'science fiction',
            'historical'      => 'historical fiction',
            'horror'          => 'horror',
            'philosophy'      => 'philosophy',
            'random'          => 'fiction',
        ];

        $searchTerm = $genreQueries[$genre] ?? 'fiction';
        $randomOffset = rand(0, 30);

        try {
            $params = [
                'q'            => 'subject:"' . $searchTerm . '"',
                'startIndex'   => $randomOffset,
                'maxResults'   => 15,
                'printType'    => 'books',
                'langRestrict' => 'en',
            ];

            if (!empty($this->googleApiKey)) {
                $params['key'] = $this->googleApiKey;
            }

            $googleRes = Http::withoutVerifying()
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->timeout(3.5)
                ->get('https://www.googleapis.com/books/v1/volumes', $params);

            if ($googleRes->successful()) {
                $items = $googleRes->json('items') ?? [];

                $validItems = array_values(array_filter($items, function ($item) {
                    $info = $item['volumeInfo'] ?? [];
                    return !empty($info['title']) &&
                           (!empty($info['imageLinks']['thumbnail']) || !empty($info['imageLinks']['smallThumbnail']));
                }));

                if (!empty($validItems)) {
                    $chosen = $validItems[array_rand($validItems)];
                    $info = $chosen['volumeInfo'] ?? [];

                    $cover = $info['imageLinks']['thumbnail'] ?? $info['imageLinks']['smallThumbnail'];
                    $cover = str_replace(['http://', '&edge=curl'], ['https://', ''], $cover);

                    Cache::put('book_meta_' . $chosen['id'], [
                        'title'       => $info['title'],
                        'authors'     => isset($info['authors']) ? implode(', ', $info['authors']) : 'Unknown Author',
                        'coverUrl'    => $cover,
                        'description' => $info['description'] ?? 'No description available.',
                        'pageCount'   => $info['pageCount'] ?? null,
                    ], 604800);

                    return response()->json([
                        'success' => true,
                        'book'    => [
                            'id'         => $chosen['id'],
                            'title'      => $info['title'],
                            'author'     => isset($info['authors']) ? implode(', ', $info['authors']) : __('Unknown Author'),
                            'cover'      => $cover,
                            'page_count' => $info['pageCount'] ?? null,
                            'url'        => route('show', $chosen['id']),
                        ]
                    ]);
                }
            }
        } catch (\Throwable $e) {}

        $randomDbBook = Book::whereNotNull('cover_image')->inRandomOrder()->first();
        if ($randomDbBook) {
            return response()->json([
                'success' => true,
                'book'    => [
                    'id'         => $randomDbBook->open_library_key ?? $randomDbBook->google_book_id ?? (string)$randomDbBook->id,
                    'title'      => $randomDbBook->title,
                    'author'     => $randomDbBook->author,
                    'cover'      => $randomDbBook->cover_image,
                    'page_count' => $randomDbBook->page_count,
                    'url'        => route('show', $randomDbBook->open_library_key ?? $randomDbBook->google_book_id ?? $randomDbBook->id),
                ]
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}