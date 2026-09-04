<?php

namespace App\Http\Controllers;

use App\Models\ReadingGoal;
use App\Models\User;
use App\Models\UserBoard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    /**
     * İki kullanıcının onaylanmış arkadaş olup olmadığını kontrol eder
     */
    private function checkFriendship($userA, $userB)
    {
        if (!$userA || !$userB) return false;
        if ($userA->id === $userB->id) return true;

        return DB::table('friendships')
            ->where('status', 'accepted')
            ->where(function ($q) use ($userA, $userB) {
                $q->where(function ($sub) use ($userA, $userB) {
                    $sub->where('user_id', $userA->id)->where('friend_id', $userB->id);
                })->orWhere(function ($sub) use ($userA, $userB) {
                    $sub->where('user_id', $userB->id)->where('friend_id', $userA->id);
                });
            })
            ->exists();
    }

    /**
     * Kullanıcının 15 rozet durumunu hesaplar
     */
    private function checkAchievements(User $user, UserBoard $board, $readThisYear, $goalTarget): array
    {
        $targetId = $user->id;
        $username = $user->username ?? $user->name ?? '';

        // 1. BURGER: Tek bir incelemesine 10+ beğeni gelmesi
        $unlockedBurger = DB::table('review_likes')
            ->join('user_books', 'review_likes.review_id', '=', 'user_books.id')
            ->where('user_books.user_id', $targetId)
            ->groupBy('review_likes.review_id')
            ->havingRaw('COUNT(*) >= 10')
            ->exists();

        // 2. YONCA: 10+ kabul edilmiş arkadaş
        $friendCount = DB::table('friendships')
            ->where('status', 'accepted')
            ->where(function ($q) use ($targetId) {
                $q->where('user_id', $targetId)->orWhere('friend_id', $targetId);
            })
            ->count();
        $unlockedYonca = ($friendCount >= 10);

        // 3. MAYMUN: 5 farklı panoya post-it bırakmak
        $authorTag = '@' . mb_strtolower(trim($username));
        $otherBoards = DB::table('user_boards')
            ->where('user_id', '!=', $targetId)
            ->whereNotNull('board_items')
            ->get(['board_items']);

        $distinctBoardsCount = 0;
        foreach ($otherBoards as $b) {
            $items = $b->board_items;
            while (is_string($items)) {
                $items = json_decode($items, true);
            }
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
        $myItems = $board->board_items;
        $uniqueAuthors = [];
        if (is_array($myItems)) {
            foreach ($myItems as $item) {
                if (($item['type'] ?? '') === 'postit' && !empty($item['author'])) {
                    $authClean = mb_strtolower(trim($item['author']));
                    if ($authClean !== $authorTag && $authClean !== mb_strtolower(trim($username))) {
                        $uniqueAuthors[$authClean] = true;
                    }
                }
            }
        }
        $unlockedAyicik = (count($uniqueAuthors) >= 5);

        // 5. ASK: 15 farklı incelemeyi beğenmek
        $unlockedAsk = DB::table('review_likes')
            ->where('user_id', $targetId)
            ->count() >= 15;

        // 6. CILEK: 10 farklı kitaba 5 yıldız vermek
        $unlockedCilek = DB::table('user_books')
            ->where('user_id', $targetId)
            ->where('rating', 5)
            ->count() >= 10;

        // 7. JAKE: 7 gün içinde 3 kitap bitirmek (status = 'read')
        $readDates = DB::table('user_books')
            ->where('user_id', $targetId)
            ->where('status', 'read')
            ->whereNotNull('updated_at')
            ->orderBy('updated_at', 'asc')
            ->pluck('updated_at')
            ->map(fn($d) => Carbon::parse($d))
            ->values();

        $unlockedJake = false;
        $readCount = count($readDates);
        if ($readCount >= 3) {
            for ($i = 0; $i <= $readCount - 3; $i++) {
                if ($readDates[$i]->diffInDays($readDates[$i + 2]) <= 7) {
                    $unlockedJake = true;
                    break;
                }
            }
        }

        // 8. KITAP: Aktif admin önerisinden en az 1 kitap ekleme
        $unlockedKitap = DB::table('user_books')
            ->join('books', 'user_books.book_id', '=', 'books.id')
            ->join('admin_recommendations', 'books.google_book_id', '=', 'admin_recommendations.book_key')
            ->where('user_books.user_id', $targetId)
            ->where('admin_recommendations.is_active', 1)
            ->exists();

        // 9. ELMA: Yıllık hedefi tamamlama
        $unlockedElma = ($goalTarget && $goalTarget > 0 && $readThisYear >= $goalTarget);

        // 10. KRUVASAN: 400+ sayfalık bir kitap bitirmek
        $unlockedKruvasan = DB::table('user_books')
            ->join('books', 'user_books.book_id', '=', 'books.id')
            ->where('user_books.user_id', $targetId)
            ->where('user_books.status', 'read')
            ->where('books.page_count', '>=', 400)
            ->exists();

        // 11. KEDI (Çerezlik): 50 sayfa ve altı bir kitap bitirmek
        $unlockedKedi = DB::table('user_books')
            ->join('books', 'user_books.book_id', '=', 'books.id')
            ->where('user_books.user_id', $targetId)
            ->where('user_books.status', 'read')
            ->where('books.page_count', '<=', 50)
            ->where('books.page_count', '>', 0)
            ->exists();

        // 12. USAGI: Aynı anda 10 kitabı okunuyor (reading) tutmak
        $unlockedUsagi = DB::table('user_books')
            ->where('user_id', $targetId)
            ->where('status', 'reading')
            ->count() >= 10;

        // 13. TAMA: Panodaki 9 kancanın hepsini doldurmak
        $filledHooks = 0;
        if (is_array($board->hook_slots)) {
            foreach ($board->hook_slots as $slot) {
                if (!empty($slot)) {
                    $filledHooks++;
                }
            }
        }
        $unlockedTama = ($filledHooks >= 9);

        // 14. GEYIK: En az 30 gündür üye olmak (bookie veteran)
        $unlockedGeyik = $user->created_at && Carbon::parse($user->created_at)->diffInDays(now()) >= 30;

        // 15. YENGEC: 5 farklı kitaba 1 yıldız vermek
        $unlockedYengec = DB::table('user_books')
            ->where('user_id', $targetId)
            ->where('rating', 1)
            ->count() >= 5;

        return [
            'ask'      => ['title' => 'Aşk',      'file' => 'aşk.png',      'unlocked' => $unlockedAsk],
            'ayicik'   => ['title' => 'Ayıcık',   'file' => 'ayıcık.png',   'unlocked' => $unlockedAyicik],
            'burger'   => ['title' => 'Burger',   'file' => 'burger.png',   'unlocked' => $unlockedBurger],
            'cilek'    => ['title' => 'Çilek',    'file' => 'çilek.png',    'unlocked' => $unlockedCilek],
            'elma'     => ['title' => 'Elma',     'file' => 'elma.png',     'unlocked' => $unlockedElma],
            'geyik'    => ['title' => 'Geyik',    'file' => 'geyik.png',    'unlocked' => $unlockedGeyik],
            'jake'     => ['title' => 'Jake',     'file' => 'jake.png',     'unlocked' => $unlockedJake],
            'kedi'     => ['title' => 'Çerezlik', 'file' => 'kedi.png',     'unlocked' => $unlockedKedi],
            'kitap'    => ['title' => 'Kitap',    'file' => 'kitap.png',    'unlocked' => $unlockedKitap],
            'kruvasan' => ['title' => 'Kruvasan', 'file' => 'kruvasan.png', 'unlocked' => $unlockedKruvasan],
            'maymun'   => ['title' => 'Maymun',   'file' => 'maymun.png',   'unlocked' => $unlockedMaymun],
            'tama'     => ['title' => 'Tama',     'file' => 'tama.png',     'unlocked' => $unlockedTama],
            'usagi'    => ['title' => 'Usagi',    'file' => 'usagi.png',    'unlocked' => $unlockedUsagi],
            'yengec'   => ['title' => 'Yengeç',   'file' => 'yengeç.png',   'unlocked' => $unlockedYengec],
            'yonca'    => ['title' => 'Yonca',    'file' => 'yonca.png',    'unlocked' => $unlockedYonca],
        ];
    }

    public function show(User $user)
    {
        $board = UserBoard::firstOrCreate(
            ['user_id' => $user->id],
            [
                'board_items' => [],
                'hook_slots' => array_fill(0, 9, null),
                'is_locked' => false,
            ]
        );

        // Katmanlı JSON çözümlemesi
        $items = $board->board_items;
        while (is_string($items)) {
            $items = json_decode($items, true);
        }
        $board->board_items = is_array($items) ? $items : [];

        $hooks = $board->hook_slots;
        while (is_string($hooks)) {
            $hooks = json_decode($hooks, true);
        }
        $board->hook_slots = is_array($hooks) ? $hooks : array_fill(0, 9, null);

        $currentUserId = auth()->id();
        $targetUserId = $user->id;
        $isOwner = auth()->check() && ($currentUserId === $targetUserId);

        $isFriend = false;
        if (auth()->check() && !$isOwner) {
            $isFriend = $this->checkFriendship(auth()->user(), $user);
        }

        // Yıllık Okuma Hedefi ve İlerleme Verileri
        $currentYear = (int) now()->format('Y');

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

        // 15 Başarım kontrolü
        $achievements = $this->checkAchievements($user, $board, $readThisYear, $goalTarget);

        return view('profile.shelf-view', compact(
            'user',
            'board',
            'achievements',
            'isFriend',
            'isOwner',
            'readingGoal',
            'readThisYear',
            'goalProgress',
            'currentYear'
        ));
    }

    public function save(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $isOwner = auth()->check() && (auth()->id() === $user->id);

        $board = UserBoard::firstOrCreate(['user_id' => $user->id]);

        if ($board->is_locked && !$isOwner) {
            return response()->json(['error' => 'Bu pano kilitlenmiştir.'], 403);
        }

        if (!$isOwner) {
            $isFriend = $this->checkFriendship($currentUser, $user);
            if (!$isFriend) {
                return response()->json(['error' => 'Sadece arkadaşlar panoya not bırakabilir.'], 403);
            }
        }

        $incoming = $request->input('board_items', []);
        while (is_string($incoming)) {
            $incoming = json_decode($incoming, true);
        }
        $board->board_items = is_array($incoming) ? $incoming : [];

        if ($isOwner) {
            if ($request->has('hook_slots')) {
                $hooks = $request->input('hook_slots');
                while (is_string($hooks)) {
                    $hooks = json_decode($hooks, true);
                }
                $board->hook_slots = is_array($hooks) ? $hooks : array_fill(0, 9, null);
            }
            if ($request->has('is_locked')) {
                $board->is_locked = $request->boolean('is_locked');
            }
        }

        $board->save();

        return response()->json([
            'success' => true,
            'saved_count' => count($board->board_items),
            'target_user_id' => $user->id
        ]);
    }

    /**
     * Yıllık okuma hedefini kaydeder (Yılda 1 kez belirlenebilir)
     */
    public function setReadingGoal(Request $request)
    {
        $request->validate([
            'target_books' => 'required|integer|min:1|max:500',
        ]);

        $currentYear = (int) now()->format('Y');
        $userId = auth()->id();

        $exists = DB::table('reading_goals')
            ->where('user_id', $userId)
            ->where('year', $currentYear)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Bu yıl için okuma hedefinizi zaten belirlediniz.'], 422);
        }

        ReadingGoal::create([
            'user_id' => $userId,
            'year' => $currentYear,
            'target_books' => $request->target_books,
        ]);

        return response()->json(['success' => true]);
    }
}