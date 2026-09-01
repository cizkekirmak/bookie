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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    private string $googleApiKey = 'AIzaSyBGjDodZWAvBQ57QjOZ24VAGHOKf2p0Pus';

    /**
     * Kapağı Storage diske indirip /storage/covers/... yolunu döner
     */
    private function getCachedCoverUrl(?string $url, string $key): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (str_starts_with($url, '/storage/') || str_starts_with($url, 'storage/')) {
            return str_starts_with($url, '/') ? $url : '/' . $url;
        }

        $url = str_replace('http://', 'https://', $url);
        $cleanKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', $key);
        $filename = 'covers/' . $cleanKey . '.jpg';
        $disk = Storage::disk('public');

        if ($disk->exists($filename)) {
            return '/storage/' . $filename;
        }

        try {
            $response = Http::withHeaders(['User-Agent' => 'BookieApp/1.0'])
                ->withOptions([
                    'verify'          => false,
                    'allow_redirects' => true,
                    'curl'            => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]
                ])
                ->timeout(6)
                ->get($url);

            if ($response->successful() && strlen($response->body()) > 300) {
                $disk->put($filename, $response->body());
                return '/storage/' . $filename;
            }
        } catch (\Throwable $e) {}

        return $url;
    }

    public function index()
    {
        $currentWeek = now()->format('Y_W');
        $cacheKey = 'popular_books_' . $currentWeek;

        // 1. Önce Cache'e, yoksa Veritabanındaki kitaplara bak
        $popularBooks = Cache::remember($cacheKey, 604800, function () use ($currentWeek) {
            // Veritabanında daha önce kaydedilmiş son popüler/klasik kitapları al
            $dbBooks = Book::whereNotNull('open_library_key')
                ->whereNotNull('cover_image')
                ->latest('updated_at')
                ->take(6)
                ->get();

            if ($dbBooks->count() >= 6) {
                return $dbBooks->map(function ($b) {
                    return [
                        'id'     => $b->open_library_key,
                        'title'  => $b->title,
                        'author' => $b->author,
                        'cover'  => str_starts_with($b->cover_image, 'http') || str_starts_with($b->cover_image, '/storage')
                            ? $b->cover_image 
                            : asset($b->cover_image),
                    ];
                })->toArray();
            }

            // Eğer DB'de henüz yeterli kitap yoksa, 1 defaya mahsus OpenLibrary'den çekip DB'ye kaydet
            try {
                $weekNumber = (int) now()->format('W');
                $offset = ($weekNumber * 5) % 40;

                $response = Http::withoutVerifying()
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                    ->timeout(4)
                    ->get('https://openlibrary.org/search.json', [
                        'q'      => 'subject:classic_literature',
                        'limit'  => 15,
                        'offset' => $offset,
                    ]);

                if ($response->successful()) {
                    $docs = $response->json('docs') ?? [];
                    $savedBooks = [];

                    foreach ($docs as $doc) {
                        $title = $doc['title'] ?? '';
                        if (empty($title) || empty($doc['cover_i'])) continue;

                        $rawKey = $doc['key'] ?? '';
                        $cleanId = str_replace(['/works/', '/books/'], '', $rawKey);
                        $olKey = 'OL_' . $cleanId;
                        $coverUrl = "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-M.jpg";
                        $author = $doc['author_name'][0] ?? 'Bilinmeyen Yazar';

                        // Veritabanına kaydet/güncelle (Böylece kalıcı olur)
                        $book = Book::updateOrCreate(
                            ['open_library_key' => $olKey],
                            [
                                'title'       => $title,
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

                    if (!empty($savedBooks)) {
                        return $savedBooks;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Haftalık popüler kitaplar çekilemedi, DB kayıtları kullanılıyor: ' . $e->getMessage());
            }

            // DB'de olan mevcut kitaplardan döndür (Asla boş kalmaz)
            return Book::whereNotNull('cover_image')->inRandomOrder()->take(6)->get()->map(function ($b) {
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

    // 1. Veritabanında Ara
    $book = Book::where('open_library_key', $cleanKey)
        ->orWhere('google_book_id', $cleanKey)
        ->orWhere('id', is_numeric($cleanKey) ? (int)$cleanKey : 0)
        ->first();

    $userBook = null;
    $allReviews = collect();

    if ($book) {
        $title = $book->title;
        $authors = $book->author;
        
        $rawCover = $book->cover_image;
        if (!empty($rawCover) && str_starts_with($rawCover, 'http')) {
            $cachedPath = $this->getCachedCoverUrl($rawCover, $cleanKey);
            if ($cachedPath !== $rawCover) {
                $book->update(['cover_image' => $cachedPath]);
                $rawCover = $cachedPath;
            }
        }

        $coverUrl = $rawCover ? (str_starts_with($rawCover, 'http') || str_starts_with($rawCover, '/storage') ? $rawCover : asset($rawCover)) : null;
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
        // 2. Cache Kontrolü
        $cacheKey = "book_meta_{$cleanKey}";
        $cached = Cache::get($cacheKey);

        if ($cached && !empty($cached['title']) && $cached['title'] !== 'Unknown book') {
            $title = $cached['title'];
            $authors = $cached['authors'] ?? 'Unknown author';
            $coverUrl = $cached['coverUrl'] ?? null;
            $description = $cached['description'] ?? 'No description available.';
        } else {
            // 3. API'den Çek
            $title = 'Unknown book';
            $authors = 'Unknown author';
            $coverUrl = null;
            $description = 'No description available.';

            // Sadece 'OL_' veya 'OL' ile BAŞLIYORSA OpenLibrary'dir
            $isOpenLibrary = str_starts_with($cleanKey, 'OL_') || str_starts_with($cleanKey, 'OL') || str_contains($cleanKey, '/works/');

            if ($isOpenLibrary) {
                try {
                    $pureWorkId = preg_replace('/^OL_+/', 'OL', str_replace('/works/', '', $cleanKey));
                    
                    $res = Http::withHeaders(['User-Agent' => 'BookieApp/1.0'])
                        ->timeout(5)
                        ->get("https://openlibrary.org/works/{$pureWorkId}.json");

                    if ($res->successful()) {
                        $data = $res->json();
                        $title = $data['title'] ?? 'Unknown book';
                        $authors = 'Open Library Author';
                        $coverId = $data['covers'][0] ?? null;
                        
                        if ($coverId) {
                            $remoteUrl = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
                            $coverUrl = $this->getCachedCoverUrl($remoteUrl, $cleanKey);
                        }
                        
                        $desc = $data['description'] ?? 'No description available.';
                        $description = is_array($desc) ? ($desc['value'] ?? '') : $desc;
                    }
                } catch (\Throwable $e) {}
            } else {
                // Google Books API Çağrısı
                try {
                    $url = "https://www.googleapis.com/books/v1/volumes/{$cleanKey}";
                    if (!empty($this->googleApiKey)) {
                        $url .= "?key=" . $this->googleApiKey;
                    }

                    $res = Http::withOptions(['verify' => false, 'curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                        ->timeout(5)
                        ->get($url);

                    if ($res->successful()) {
                        $info = $res->json()['volumeInfo'] ?? [];
                        $title = $info['title'] ?? 'Unknown book';
                        $authors = isset($info['authors']) ? implode(', ', $info['authors']) : 'Unknown author';
                        $rawCover = $info['imageLinks']['thumbnail'] ?? ($info['imageLinks']['smallThumbnail'] ?? null);
                        
                        if ($rawCover) {
                            $remoteUrl = str_replace(['http://', '&edge=curl'], ['https://', ''], $rawCover);
                            $coverUrl = $this->getCachedCoverUrl($remoteUrl, $cleanKey);
                        }
                        $description = $info['description'] ?? 'No description available.';
                    }
                } catch (\Throwable $e) {}
            }

            if (!empty($coverUrl) || $title !== 'Unknown book') {
                Cache::put($cacheKey, [
                    'title'       => $title,
                    'authors'     => $authors,
                    'coverUrl'    => $coverUrl,
                    'description' => $description,
                ], 604800);
            }
        }
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
        'totalReviews'
    ));
}
    /**
     * Arama API
     */
    public function searchApi(Request $request)
{
    $query = trim($request->get('q', ''));

    if (mb_strlen($query) < 2) {
        return response()->json([]);
    }

    // 1. Cache Kontrolü
    $cacheKey = 'search_safe_' . md5(mb_strtolower($query, 'UTF-8'));
    if (Cache::has($cacheKey)) {
        $cached = Cache::get($cacheKey);
        if (!empty($cached)) {
            return response()->json($cached);
        }
    }

    // 2. Lokal Veritabanından Al
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
                'cover'   => str_starts_with($b->cover_image, 'http') || str_starts_with($b->cover_image, '/storage')
                    ? $b->cover_image 
                    : asset($b->cover_image),
            ];
        })->toArray();

    // 3. Google Books ve Open Library'den Çek
    $googleResults = $this->searchGoogleBooks($query);
    $openLibResults = $this->searchOpenLibrary($query);

    // Hepsini birleştir (Lokal + Google + Open Library)
    $merged = array_merge($localBooks, $googleResults, $openLibResults);
    
    // Alakasız kelime temizliği
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

        // Başlıkta veya yazarda aranan kelimelerden en az biri geçmeli
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

        // Başlık + Yazar kombinasyonuyla tekilleştir (Böylece farklı baskılar ve Google sonuçları kaybolmaz)
        $uniqueKey = $titleLower . '_' . mb_substr($authorLower, 0, 8);
        if (!isset($seenKeys[$uniqueKey])) {
            $seenKeys[$uniqueKey] = true;
            $filteredResults[] = $item;
        }
    }

    // 4. Sıralama (Tam eşleşenler ve aranan kelimeyle başlayanlar en üstte)
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

    // 5. Cache'e kaydet
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
                'q'          => 'intitle:' . $query, // Sadece kitap başlığına odaklan
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
                // Kapak ID'si yoksa kitabı listeye hiç alma
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

        if ($genre === 'random' || empty($genre)) {
            $genres = ['fantasy', 'mystery', 'romance', 'science_fiction', 'historical', 'thriller', 'horror', 'biography', 'self-help', 'philosophy'];
            $genre = $genres[array_rand($genres)];
        }

        try {
            $response = Http::timeout(8)->get('https://openlibrary.org/subjects/' . urlencode($genre) . '.json?limit=25');

            if ($response->successful()) {
                $works = $response->json('works', []);
                $filtered = array_filter($works, fn($item) => !empty($item['cover_id']) && !empty($item['title']));

                if (!empty($filtered)) {
                    $randomWork = $filtered[array_rand($filtered)];
                    $openLibraryKey = str_replace('/works/', '', $randomWork['key'] ?? '');
                    $title = $randomWork['title'] ?? 'Unknown Title';

                    $author = 'Unknown Author';
                    if (!empty($randomWork['authors'])) {
                        $author = is_array($randomWork['authors'][0]) 
                            ? ($randomWork['authors'][0]['name'] ?? 'Unknown Author') 
                            : $randomWork['authors'][0];
                    }

                    $coverUrl = "https://covers.openlibrary.org/b/id/{$randomWork['cover_id']}-M.jpg";

                    // Veritabanında kitap yoksa indir ve kaydet
                    $book = Book::where('open_library_key', $openLibraryKey)->first();

                    if (!$book) {
                        $localCoverPath = $this->downloadCoverImage($coverUrl, $openLibraryKey);

                        $book = Book::create([
                            'open_library_key' => $openLibraryKey,
                            'title'            => $title,
                            'author'           => $author,
                            'cover_image'      => $localCoverPath ?? $coverUrl,
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'book'    => [
                            'id'     => $book->open_library_key,
                            'title'  => $book->title,
                            'author' => $book->author,
                            'cover'  => $book->cover_image,
                            'url'    => route('show', $book->open_library_key ?? $book->id),
                        ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            $randomDbBook = Book::inRandomOrder()->first();
            if ($randomDbBook) {
                return response()->json([
                    'success' => true,
                    'book'    => [
                        'id'     => $randomDbBook->open_library_key ?? $randomDbBook->google_book_id ?? (string)$randomDbBook->id,
                        'title'  => $randomDbBook->title,
                        'author' => $randomDbBook->author,
                        'cover'  => $randomDbBook->cover_image,
                        'url'    => route('show', $randomDbBook->open_library_key ?? $randomDbBook->id),
                    ]
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'No recommendation available.'], 404);
    }

    /**
     * Dış bağlantıdaki kapağı indirip storage/app/public/covers/ altına kaydeder.
     * DB'ye '/storage/covers/OL_KEY.jpg' formatında döner.
     */
    private function downloadCoverImage(?string $url, string $openLibraryKey): ?string
    {
        if (empty($url)) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $filename = 'covers/OL_' . $openLibraryKey . '.jpg';
                Storage::disk('public')->put($filename, $response->body());

                return '/storage/' . $filename;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

}