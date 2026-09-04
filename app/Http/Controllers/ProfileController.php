<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\UserBook;
use App\Models\User;
use App\Models\friendship;
use App\Models\UserBoard;

class ProfileController extends Controller
{
    public function show($id = null)
    {
        $auth = auth()->user();

        $user = ($id && $id != $auth->id) ? User::findOrFail($id) : $auth;
        $isOwnProfile = ($user->id === $auth->id);

        $userBooks = UserBook::whereHas('book')
            ->with('book')
            ->where("user_id", $user->id)
            ->latest()
            ->get();

        $booksCount = $userBooks->count();
        $friendsCount = $user->friends()->count();

        $friendship = null;
        if (!$isOwnProfile) {
            $friendship = friendship::where(function ($q) use ($user, $auth) {
                $q->where("user_id", $auth->id)->where("friend_id", $user->id);
            })->orWhere(function($q) use ($user, $auth) {
                $q->where("user_id", $user->id)->where("friend_id", $auth->id);
            })->first();
        }

        $pendingRequests = friendship::with("sender")
            ->where("friend_id", $auth->id)
            ->where("status", "pending")
            ->get();

        // --- YILLIK OKUMA HEDEFİ HESAPLAMALARI ---
        $currentYear = (int) now()->format('Y');
        $targetUserId = $user->id;
        $username = $user->username ?? $user->name ?? '';

        $readingGoal = DB::table('reading_goals')
            ->where('user_id', $targetUserId)
            ->where('year', $currentYear)
            ->first();

        $readThisYear = DB::table('user_books')
            ->where('user_id', $targetUserId)
            ->where('status', 'read')
            ->whereYear('updated_at', $currentYear)
            ->count();

        $goalTarget = $readingGoal ? $readingGoal->target_books : null;
        $goalProgress = $goalTarget ? min(100, round(($readThisYear / $goalTarget) * 100)) : 0;

        // --- PANO (BOARD) VE KANCA BİLGİLERİ ---
        $board = UserBoard::firstOrCreate(
            ['user_id' => $targetUserId],
            [
                'board_items' => [],
                'hook_slots' => array_fill(0, 9, null),
                'is_locked' => false,
            ]
        );

        $bItems = $board->board_items;
        while (is_string($bItems)) { $bItems = json_decode($bItems, true); }
        $board->board_items = is_array($bItems) ? $bItems : [];

        $hSlots = $board->hook_slots;
        while (is_string($hSlots)) { $hSlots = json_decode($hSlots, true); }
        $board->hook_slots = is_array($hSlots) ? $hSlots : array_fill(0, 9, null);

        // --- 15 BAŞARIMIN DİNAMİK KONTROLLERİ ---

        $unlockedBurger = DB::table('review_likes')
            ->join('user_books', 'review_likes.review_id', '=', 'user_books.id')
            ->where('user_books.user_id', $targetUserId)
            ->select('review_likes.review_id')
            ->groupBy('review_likes.review_id')
            ->havingRaw('COUNT(*) >= 10')
            ->exists();

        // 2. YONCA: 10+ kabul edilmiş arkadaş
        $unlockedYonca = DB::table('friendships')
            ->where('status', 'accepted')
            ->where(function ($q) use ($targetUserId) {
                $q->where('user_id', $targetUserId)->orWhere('friend_id', $targetUserId);
            })->count() >= 10;

        // 3. MAYMUN: 5 farklı panoya post-it bırakmak
        $authorTag = '@' . mb_strtolower(trim($username));
        $otherBoards = DB::table('user_boards')
            ->where('user_id', '!=', $targetUserId)
            ->whereNotNull('board_items')
            ->get(['board_items']);
        $distinctBoardsCount = 0;
        foreach ($otherBoards as $b) {
            $items = $b->board_items;
            while (is_string($items)) { $items = json_decode($items, true); }
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (($item['type'] ?? '') === 'postit' && !empty($item['author'])) {
                        $authClean = mb_strtolower(trim($item['author']));
                        if (str_contains($authClean, $authorTag) || $authClean === mb_strtolower(trim($username))) {
                            $distinctBoardsCount++;
                            break;
                        }
                    }
                }
            }
        }
        $unlockedMaymun = ($distinctBoardsCount >= 5);

        // 4. AYICIK: Kendi panosuna 5 farklı kişiden not gelmesi
        $uniqueAuthors = [];
        foreach ($board->board_items as $item) {
            if (($item['type'] ?? '') === 'postit' && !empty($item['author'])) {
                $authClean = mb_strtolower(trim($item['author']));
                if ($authClean !== $authorTag && $authClean !== mb_strtolower(trim($username))) {
                    $uniqueAuthors[$authClean] = true;
                }
            }
        }
        $unlockedAyicik = (count($uniqueAuthors) >= 5);

        // 5. ASK: 15 farklı incelemeyi beğenmek
        $unlockedAsk = DB::table('review_likes')->where('user_id', $targetUserId)->count() >= 15;

        // 6. CILEK: 10 farklı kitaba 5 yıldız vermek
        $unlockedCilek = DB::table('user_books')->where('user_id', $targetUserId)->where('rating', 5)->count() >= 10;

        // 7. JAKE: 7 gün içinde 3 kitap bitirmek
        $readDates = DB::table('user_books')
            ->where('user_id', $targetUserId)
            ->where('status', 'read')
            ->whereNotNull('updated_at')
            ->orderBy('updated_at', 'asc')
            ->pluck('updated_at')
            ->map(fn($d) => Carbon::parse($d))
            ->values();
        $unlockedJake = false;
        if (count($readDates) >= 3) {
            for ($i = 0; $i <= count($readDates) - 3; $i++) {
                if ($readDates[$i]->diffInDays($readDates[$i + 2]) <= 7) {
                    $unlockedJake = true;
                    break;
                }
            }
        }

        // 8. KITAP: Aktif admin önerilerinden en az 1 kitap eklemek
        $unlockedKitap = DB::table('user_books')
            ->join('books', 'user_books.book_id', '=', 'books.id')
            ->join('admin_recommendations', 'books.google_book_id', '=', 'admin_recommendations.book_key')
            ->where('user_books.user_id', $targetUserId)
            ->where('admin_recommendations.is_active', 1)
            ->exists();

        // 9. ELMA: Yıllık hedefi tamamlama
        $unlockedElma = ($goalTarget && $goalTarget > 0 && $readThisYear >= $goalTarget);

        // 10. KRUVASAN: 400+ sayfa kitap bitirme
        $unlockedKruvasan = DB::table('user_books')
            ->join('books', 'user_books.book_id', '=', 'books.id')
            ->where('user_books.user_id', $targetUserId)
            ->where('user_books.status', 'read')
            ->where('books.page_count', '>=', 400)
            ->exists();

        // 11. KEDI (Çerezlik): 50 sayfa ve altı bir kitap bitirme
        $unlockedKedi = DB::table('user_books')
            ->join('books', 'user_books.book_id', '=', 'books.id')
            ->where('user_books.user_id', $targetUserId)
            ->where('user_books.status', 'read')
            ->where('books.page_count', '<=', 50)
            ->where('books.page_count', '>', 0)
            ->exists();

        // 12. USAGI: Aynı anda 10 kitabı okunuyor (reading) tutma
        $unlockedUsagi = DB::table('user_books')
            ->where('user_id', $targetUserId)
            ->where('status', 'reading')
            ->count() >= 10;

        // 13. TAMA: Panodaki 9 kancanın hepsini doldurma
        $filledHooks = count(array_filter($board->hook_slots, fn($s) => !empty($s)));
        $unlockedTama = ($filledHooks >= 9);

        // 14. GEYIK: En az 30 gündür üye olma (müdavim)
        $unlockedGeyik = $user->created_at && Carbon::parse($user->created_at)->diffInDays(now()) >= 30;

        // 15. YENGEC: 5 farklı kitaba 1 yıldız verme
        $unlockedYengec = DB::table('user_books')
            ->where('user_id', $targetUserId)
            ->where('rating', 1)
            ->count() >= 5;

        // Seçtiğimiz havalı isimler ve açıklamalar
        // Çoklu dil (localization) destekli başarım listesi
        // Çoklu dil (localization) destekli başarım listesi (Varsayılan İngilizce)
        $keychains = [
            'ask'      => [
                'name'     => __('Book Lover'),
                'file'     => 'aşk.png',
                'unlocked' => $unlockedAsk,
                'desc'     => __('Like 15 different reviews')
            ],
            'ayicik'   => [
                'name'     => __('Local Chief'),
                'file'     => 'ayıcık.png',
                'unlocked' => $unlockedAyicik,
                'desc'     => __('Receive notes from 5 different users on your board')
            ],
            'burger'   => [
                'name'     => __('Gourmet Critic'),
                'file'     => 'burger.png',
                'unlocked' => $unlockedBurger,
                'desc'     => __('Get 10+ likes on a single review')
            ],
            'cilek'    => [
                'name'     => __('Generous Reader'),
                'file'     => 'çilek.png',
                'unlocked' => $unlockedCilek,
                'desc'     => __('Give 5 stars to 10 different books')
            ],
            'elma'     => [
                'name'     => __('Goal Crusher'),
                'file'     => 'elma.png',
                'unlocked' => $unlockedElma,
                'desc'     => __('Complete your annual reading goal')
            ],
            'geyik'    => [
                'name'     => __('Bookie Veteran'),
                'file'     => 'geyik.png',
                'unlocked' => $unlockedGeyik,
                'desc'     => __('Be a member of Bookie for at least 30 days')
            ],
            'jake'     => [
                'name'     => __('Speedrunner'),
                'file'     => 'jake.png',
                'unlocked' => $unlockedJake,
                'desc'     => __('Finish 3 books in a single week')
            ],
            'kedi'     => [
                'name'     => __('Bite-sized'),
                'file'     => 'kedi.png',
                'unlocked' => $unlockedKedi,
                'desc'     => __('Finish a book with 50 pages or less')
            ],
            'kitap'    => [
                'name'     => __("Editor's Choice"),
                'file'     => 'kitap.png',
                'unlocked' => $unlockedKitap,
                'desc'     => __('Add a book from the editor recommendations')
            ],
            'kruvasan' => [
                'name'     => __('Heavyweight'),
                'file'     => 'kruvasan.png',
                'unlocked' => $unlockedKruvasan,
                'desc'     => __('Finish a book with 400+ pages')
            ],
            'maymun'   => [
                'name'     => __('Carrier Pigeon'),
                'file'     => 'maymun.png',
                'unlocked' => $unlockedMaymun,
                'desc'     => __('Leave a note on 5 different user boards')
            ],
            'tama'     => [
                'name'     => __('Hook Master'),
                'file'     => 'tama.png',
                'unlocked' => $unlockedTama,
                'desc'     => __('Fill all 9 hooks on your board')
            ],
            'usagi'    => [
                'name'     => __('Overambitious'),
                'file'     => 'usagi.png',
                'unlocked' => $unlockedUsagi,
                'desc'     => __('Have 10 books in currently reading status simultaneously')
            ],
            'yengec'   => [
                'name'     => __('Harsh Judge'),
                'file'     => 'yengeç.png',
                'unlocked' => $unlockedYengec,
                'desc'     => __('Give 1 star to 5 different books')
            ],
            'yonca'    => [
                'name'     => __('Social Butterfly'),
                'file'     => 'yonca.png',
                'unlocked' => $unlockedYonca,
                'desc'     => __('Have at least 10 accepted friends')
            ],
        ];

        // Geriye uyumluluk için achievements da gönderiyoruz
        $achievements = $keychains;

        return view("profile", compact(
            "user", 
            "userBooks", 
            "booksCount", 
            "friendsCount", 
            "pendingRequests", 
            "friendship", 
            "isOwnProfile",
            "readingGoal",
            "readThisYear",
            "goalProgress",
            "currentYear",
            "board",
            "keychains",
            "achievements"
        ));
    }

    public function settings()
    {
        $user = auth()->user();
        return view("ayarlar", compact("user"));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'bio'    => 'nullable|string|max:160',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $cloudName = env('CLOUDINARY_CLOUD_NAME', 'fxyz37re');
            $apiKey    = env('CLOUDINARY_API_KEY', '899324875961436');
            $apiSecret = env('CLOUDINARY_API_SECRET', '8z8E_0aF6fG7Q68bCq48b9v2NqI');

            $timestamp = time();
            $folder    = 'bookie_avatars';

            $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
            $signature    = sha1($paramsToSign . $apiSecret);

            try {
                $response = Http::withoutVerifying()
                    ->asMultipart()
                    ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                        ['name' => 'file', 'contents' => fopen($file->getRealPath(), 'r'), 'filename' => $file->getClientOriginalName()],
                        ['name' => 'api_key', 'contents' => $apiKey],
                        ['name' => 'timestamp', 'contents' => (string)$timestamp],
                        ['name' => 'folder', 'contents' => $folder],
                        ['name' => 'signature', 'contents' => $signature],
                    ]);

                if ($response->successful()) {
                    $user->avatar = $response->json('secure_url');
                } else {
                    Log::error('Cloudinary Avatar Hatası: ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Cloudinary Bağlantı Hatası: ' . $e->getMessage());
            }
        }

        $user->bio = $request->input('bio');
        $user->save();

        return redirect()->back()->with('success', __('Profile updated successfully!'));
    }

    public function bulkRemoveBooks(Request $request)
    {
        $ids = $request->input('selected_books', []);

        if (!empty($ids)) {
            UserBook::whereIn('id', $ids)
                ->where('user_id', auth()->id())
                ->delete();

            return redirect()->route('profile')->with('success', __(':count books removed from your bookshelf! 🗑️', ['count' => count($ids)]));
        }

        return redirect()->route('profile');
    }
}