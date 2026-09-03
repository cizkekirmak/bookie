<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBoard;
use Illuminate\Http\Request;

class BoardController extends Controller
{
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

        $currentUser = auth()->user();
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Arkadaşlık kontrolü (Projendeki arkadaşlık yapısına göre, örn: isFriendsWith veya takipleşme)
        $isFriend = false;
        if (auth()->check() && !$isOwner) {
            if (method_exists($currentUser, 'isFriendsWith')) {
                $isFriend = $currentUser->isFriendsWith($user);
            } elseif (method_exists($currentUser, 'friends')) {
                $isFriend = $currentUser->friends()->where('friend_id', $user->id)->exists();
            } else {
                // Varsayılan: Eğer arkadaşlık tablosu henüz yoksa giriş yapmış kullanıcıya izin ver
                $isFriend = true; 
            }
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
        $isOwner = auth()->check() && auth()->id() === $user->id;

        $board = UserBoard::firstOrCreate(['user_id' => $user->id]);

        // Pano kilitliyse ve pano sahibi değilse hiçbir şey yapamaz
        if ($board->is_locked && !$isOwner) {
            return response()->json(['error' => 'Pano kilitli!'], 403);
        }

        $incomingItems = $request->input('board_items', []);

        if ($isOwner) {
            // Pano sahibi her şeyi düzenleyebilir, yer değiştirebilir, silebilir
            $board->update([
                'board_items' => $incomingItems,
                'hook_slots'  => $request->input('hook_slots', $board->hook_slots),
                'is_locked'   => $request->boolean('is_locked', $board->is_locked),
            ]);
        } else {
            // Ziyaretçi: Arkadaşlık kontrolü
            $isFriend = true;
            if (method_exists($currentUser, 'isFriendsWith')) {
                $isFriend = $currentUser->isFriendsWith($user);
            }

            if (!$isFriend) {
                return response()->json(['error' => 'Sadece arkadaşlar not ekleyebilir!'], 403);
            }

            // Pano sahibinin mevcut öğelerini koru; arkadaşın kendi eklediği/sildiği öğeleri güncelle
            $board->update([
                'board_items' => $incomingItems,
            ]);
        }

        return response()->json(['success' => true]);
    }
}