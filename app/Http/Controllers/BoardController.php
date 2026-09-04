<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBoard;
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

        return view('profile.shelf-view', compact('user', 'board', 'achievements', 'isFriend', 'isOwner'));
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

        // Gelen veriyi çöz
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
}