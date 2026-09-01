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

        // Zaten Cloudinary üzerindeyse tekrar yükleme
        if (str_contains($url, 'res.cloudinary.com')) {
            return $url;
        }

        $cloudName = env('CLOUDINARY_CLOUD_NAME', 'fxyz37re');
        $apiKey    = env('CLOUDINARY_API_KEY', '899324875961436');
        $apiSecret = env('CLOUDINARY_API_SECRET', '8z8E_0aF6fG7Q68bCq48b9v2NqI');

        $timestamp = time();
        $folder    = 'bookie_covers';
        $publicId  = 'cover_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $key);

        // İmza üretimi (Parametreler alfabetik sırada olmalı)
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
            } else {
                Log::warning('Cloudinary kapak API yanıt hatası: ' . $response->body());
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

            if (empty($pageCount) && !empty($title) && $title !== 'Unknown book' && $title !== 'Unknown Title') {
                try {
                    $params = ['q' => 'intitle:' . $title, 'maxResults' => 1];
                    if (!empty($this->googleApiKey)) {
                        $params['key'] = $this->googleApiKey;
                    }

                    $gRes = Http::withoutVerifying()
                        ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                        ->timeout(3)
                        ->get('https://www.googleapis.com/books/v1/volumes', $params);

                    if ($gRes->successful()) {
                        $foundPages = $gRes->json('items.0.volumeInfo.pageCount');
                        if ($foundPages) {
                            $pageCount = (int)$foundPages;
                            $book->update(['page_count' => $pageCount]);
                        }
                    }
                } catch (\Throwable $e) {}
            }

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

    public function fetchPageCount($key)
    {
        $cleanKey = trim((string)$key);
        $book = Book::where('open_library_key', $cleanKey)
            ->orWhere('google_book_id', $cleanKey)
            ->orWhere('id', is_numeric($cleanKey) ? (int)$cleanKey : 0)
            ->first();

        if ($book && $book->page_count > 0) {
            return response()->json(['page_count' => $book->page_count]);
        }

        $pageCount = null;

        if (str_contains($cleanKey, 'OL') || str_contains($cleanKey, '/works/')) {
            try {
                $pureWorkId = preg_replace('/^(OL_)+/', '', str_replace('/works/', '', $cleanKey));
                if (!str_starts_with($pureWorkId, 'OL')) {
                    $pureWorkId = 'OL' . $pureWorkId;
                }

                $res = Http::withHeaders(['User-Agent' => 'BookieApp/1.0'])
                    ->withoutVerifying()
                    ->timeout(3)
                    ->get('https://openlibrary.org/search.json', [
                        'q'     => 'key:/works/' . $pureWorkId,
                        'limit' => 1
                    ]);

                if ($res->successful()) {
                    $doc = $res->json('docs.0');
                    $pageCount = $doc['number_of_pages_median'] ?? ($doc['number_of_pages'] ?? null);
                }
            } catch (\Throwable $e) {}
        }

        if (empty($pageCount)) {
            try {
                $title = $book->title ?? '';
                $googleQuery = !empty($title) && $title !== 'Unknown book' ? 'intitle:' . $title : $cleanKey;
                $params = ['q' => $googleQuery, 'maxResults' => 1];
                if (!empty($this->googleApiKey)) {
                    $params['key'] = $this->googleApiKey;
                }

                $res = Http::withOptions(['verify' => false, 'curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                    ->timeout(3)
                    ->get('https://www.googleapis.com/books/v1/volumes', $params);

                if ($res->successful()) {
                    $items = $res->json('items');
                    $pageCount = $items[0]['volumeInfo']['pageCount'] ?? null;
                }
            } catch (\Throwable $e) {}
        }

        if ($book && $pageCount) {
            $book->update(['page_count' => (int)$pageCount]);
        }

        return response()->json(['page_count' => $pageCount ? (int)$pageCount : null]);
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
            $authorLower = mb_strtolower($item['authors'], 'UTF-8');

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
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
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
                'page_count'       => $request->filled('page_count') ? (int)$request->input('page_count') : null,
            ]);
        } else {
            $updates = [];
            if (empty($book->cover_image) && !empty($coverValue)) {
                $updates['cover_image'] = $coverValue;
            }
            if ($request->filled('page_count') && empty($book->page_count)) {
                $updates['page_count'] = (int)$request->input('page_count');
            }
            if (!empty($updates)) {
                $book->update($updates);
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
                'status'       => $status,
                'current_page' => $request->current_page ?? 0,
                'rating'       => $request->filled('rating') ? (int)$request->rating : null,
                'review'       => $request->filled('review') ? $request->review : null,
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

    $genreMap = [
        'fantasy'         => 'subject:fantasy',
        'classic'         => 'subject:classic+literature',
        'romance'         => 'subject:romance',
        'science_fiction' => 'subject:science+fiction',
        'historical'      => 'subject:history',
        'horror'          => 'subject:horror',
        'philosophy'      => 'subject:philosophy',
        'random'          => 'subject:fiction',
    ];

    $query = $genreMap[$genre] ?? 'subject:fiction';
    $randomOffset = rand(0, 20); // Her aramada farklı bir kitap kümesi

    // ----------------------------------------------------
    // 1. ÖNCELİK: Google Books API (Işık hızında & Geniş Arşiv)
    // ----------------------------------------------------
    try {
        $googleRes = Http::timeout(3)->get('https://www.googleapis.com/books/v1/volumes', [
            'q'            => $query,
            'startIndex'   => $randomOffset,
            'maxResults'   => 10,
            'langRestrict' => 'en',
            'printType'    => 'books',
        ]);

        if ($googleRes->successful()) {
            $items = $googleRes->json('items') ?? [];
            
            // Sadece kapağı ve başlığı olanları filtrele
            $validItems = array_filter($items, function($item) {
                return !empty($item['volumeInfo']['imageLinks']['thumbnail']) 
                    || !empty($item['volumeInfo']['imageLinks']['smallThumbnail']);
            });

            if (!empty($validItems)) {
                $item = $validItems[array_rand($validItems)];
                $info = $item['volumeInfo'] ?? [];

                $cover = $info['imageLinks']['thumbnail'] ?? $info['imageLinks']['smallThumbnail'];
                $cover = str_replace('http://', 'https://', $cover);

                return response()->json([
                    'success' => true,
                    'book'    => [
                        'id'         => $item['id'],
                        'title'      => $info['title'] ?? 'Unknown Title',
                        'author'     => isset($info['authors']) ? implode(', ', $info['authors']) : __('Unknown Author'),
                        'cover'      => $cover,
                        'page_count' => $info['pageCount'] ?? null,
                        'url'        => url('/books/' . $item['id']),
                    ]
                ]);
            }
        }
    } catch (\Throwable $e) {
        // Google Books başarısız olursa Open Library'e geç
    }

    // ----------------------------------------------------
    // 2. YEDEK: Open Library API
    // ----------------------------------------------------
    try {
        $cleanGenre = ($genre === 'random' || empty($genre)) ? 'fiction' : $genre;
        $openLibRes = Http::withHeaders(['User-Agent' => 'BookieApp/1.0'])
            ->timeout(3)
            ->get("https://openlibrary.org/subjects/{$cleanGenre}.json?limit=20");

        if ($openLibRes->successful()) {
            $works = $openLibRes->json('works', []);
            $filtered = array_filter($works, fn($item) => !empty($item['cover_id']) && !empty($item['title']));

            if (!empty($filtered)) {
                $randomWork = $filtered[array_rand($filtered)];
                $key = str_replace('/works/', '', $randomWork['key'] ?? '');
                
                return response()->json([
                    'success' => true,
                    'book'    => [
                        'id'         => $key,
                        'title'      => $randomWork['title'] ?? 'Unknown Title',
                        'author'     => $randomWork['authors'][0]['name'] ?? __('Unknown Author'),
                        'cover'      => "https://covers.openlibrary.org/b/id/{$randomWork['cover_id']}-M.jpg",
                        'page_count' => $randomWork['number_of_pages_median'] ?? null,
                        'url'        => route('show', $key),
                    ]
                ]);
            }
        }
    } catch (\Throwable $e) {
        // Open Library de yanıt vermezse DB fallback
    }

    // ----------------------------------------------------
    // 3. SON ÇARE: Veritabanından Rastgele Seçim (Sabit ilk kayıt DEĞİL)
    // ----------------------------------------------------
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
                'url'        => route('show', $randomDbBook->open_library_key ?? $randomDbBook->id),
            ]
        ]);
    }

    return response()->json(['success' => false, 'message' => 'No recommendation available.'], 404);
}
    private function downloadCoverImage(?string $url, string $openLibraryKey): ?string
    {
        return $this->getCachedCoverUrl($url, $openLibraryKey);
    }
}