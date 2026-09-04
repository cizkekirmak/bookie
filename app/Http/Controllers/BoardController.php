<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBoard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    /**
     * İki kullanıcının arkadaş olup olmadığını net kontrol eden yardımcı fonksiyon
     */
    private function checkFriendship($userA, $userB)
    {
        if (!$userA || !$userB) return false;
        if ($userA->id === $userB->id) return true; // Kendi profili

        // 1. Eğer User modelinde tanımlı bir ilişki/fonksiyon varsa:
        if (method_exists($userA, 'isFriendsWith')) {
            return $userA->isFriendsWith($userB);
        }

        // 2. Klasik friends / friendships tablosu kontrolü (Onaylanmış arkadaşlık)
        // Projendeki tablo adına göre 'friends' veya 'friendships' kontrol edilir:
        $hasFriendTable = \Schema::hasTable('friendships') || \Schema::hasTable('friends');
        if ($hasFriendTable) {
            $table = \Schema::hasTable('friendships') ? 'friendships' : 'friends';
            
            return DB::table($table)
                ->where(function($q) use ($userA, $userB) {
                    $q->where('user_id', $userA->id)->where('friend_id', $userB->id);
                })
                ->orWhere(function($q) use ($userA, $userB) {
                    $q->where('user_id', $userB->id)->where('friend_id', $userA->id);
                })
                // Eğer tabloda 'status' (accepted/confirmed vb.) kolonu varsa:
                ->when(\Schema::hasColumn($table, 'status'), function($q) {
                    $q->where('status', 'accepted');
                })
                ->exists();
        }

        // 3. Eğer sistemin takip (follows / followers) üzerine kuruluysa:
        if (\Schema::hasTable('follows')) {
            // Karşılıklı takipleşme = Arkadaşlık
            $followsTarget = DB::table('follows')->where('follower_id', $userA->id)->where('following_id', $userB->id)->exists();
            $targetFollows = DB::table('follows')->where('follower_id', $userB->id)->where('following_id', $userA->id)->exists();
            return $followsTarget && $targetFollows;
        }

        return false;
    }

    public function show($username)
{
    // Kullanıcıyı username üzerinden kesin olarak bul
    $user = User::where('username', $username)->firstOrFail();

    $board = UserBoard::firstOrCreate(
        ['user_id' => $user->id],
        [
            'board_items' => [],
            'hook_slots' => array_fill(0, 9, null),
            'is_locked' => false,
        ]
    );

    $currentUserId = auth()->id();
    $targetUserId = $user->id;
    $isOwner = auth()->check() && ($currentUserId === $targetUserId);
    
    // Doğrudan veritabanındaki friendships tablosundan kesin kontrol
    $isFriend = false;
    if (auth()->check() && !$isOwner) {
        $isFriend = \DB::table('friendships')
            ->where('status', 'accepted')
            ->where(function ($q) use ($currentUserId, $targetUserId) {
                $q->where(function($sub) use ($currentUserId, $targetUserId) {
                    $sub->where('user_id', $currentUserId)->where('friend_id', $targetUserId);
                })->orWhere(function($sub) use ($currentUserId, $targetUserId) {
                    $sub->where('user_id', $targetUserId)->where('friend_id', $currentUserId);
                });
            })
            ->exists();
    }

    $achievements = [
        'ask'       => ['title' => 'Aşk',       'file' => 'aşk.png',       'unlocked' => true],
        'ayicik'    => ['title' => 'Ayıcık',    'file' => 'ayıcık.png',    'unlocked' => true],
        'burger'    => ['title' => 'Burger',    'file' => 'burger.png',    'unlocked' => false],
        'cilek'     => ['title' => 'Çilek',     'file' => 'çilek.png',     'unlocked' => true],
        'elma'      => ['title' => 'Elma',      'file' => 'elma.png',      'unlocked' => false],
        'geyik'     => ['title' => 'Geyik',     'file' => 'geyik.png',     'unlocked' => false],
        'jake'      => ['title' => 'Jake',      'file' => 'jake.png',      'unlocked' => true],
        'kedi'      => ['title' => 'Kedi',      'file' => 'kedi.png',      'unlocked' => false],
        'kitap'     => ['title' => 'Kitap',     'file' => 'kitap.png',     'unlocked' => true],
        'kruvasan'  => ['title' => 'Kruvasan',  'file' => 'kruvasan.png',  'unlocked' => false],
        'maymun'    => ['title' => 'Maymun',    'file' => 'maymun.png',    'unlocked' => true],
        'tama'      => ['title' => 'Tama',      'file' => 'tama.png',      'unlocked' => false],
        'usagi'     => ['title' => 'Usagi',     'file' => 'usagi.png',     'unlocked' => true],
        'yengec'    => ['title' => 'Yengeç',    'file' => 'yengeç.png',    'unlocked' => false],
        'yonca'     => ['title' => 'Yonca',     'file' => 'yonca.png',     'unlocked' => false],
    ];

    return view('board', compact('user', 'board', 'achievements', 'isFriend', 'isOwner'));
}

    public function save(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $isOwner = auth()->check() && (auth()->id() === $user->id);

        $board = UserBoard::firstOrCreate(['user_id' => $user->id]);

        // Pano kilitliyse ve sahibi değilse direkt ret
        if ($board->is_locked && !$isOwner) {
            return response()->json(['error' => 'Bu pano kilitlenmiştir.'], 403);
        }

        // Giriş yapmış ama arkadaş değilse (ve sahibi de değilse) direkt ret
        if (!$isOwner) {
            $isFriend = $this->checkFriendship($currentUser, $user);
            if (!$isFriend) {
                return response()->json(['error' => 'Sadece arkadaşlar panoya not bırakabilir.'], 403);
            }
        }

        $incomingItems = $request->input('board_items', []);

        if ($isOwner) {
            // Pano sahibi anahtarlıkları, kilit durumunu ve her şeyi günceller
            $board->update([
                'board_items' => $incomingItems,
                'hook_slots'  => $request->input('hook_slots', $board->hook_slots),
                'is_locked'   => $request->boolean('is_locked', $board->is_locked),
            ]);
        } else {
            // Arkadaş ise kancaları veya kilidi değiştiremez, yalnızca post-it ekleyebilir/silebilir
            $board->update([
                'board_items' => $incomingItems,
            ]);
        }

        return response()->json(['success' => true]);
    }
}