<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBoard;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    // Panoyu Görüntüleme
    public function show(User $user)
    {
        $board = UserBoard::firstOrCreate(
            ['user_id' => $user->id],
            [
                'board_items' => [],
                'hook_slots' => array_fill(0, 9, null), // 9 boş kanca
            ]
        );

        // Standart anahtarlık listesi (Temiz slug'lar)
        $achievements = [
            'ask'      => ['title' => 'Aşk',      'file' => 'aşk.png',      'unlocked' => true],
            'ayicik'   => ['title' => 'Ayıcık',   'file' => 'ayıcık.png',   'unlocked' => true],
            'burger'   => ['title' => 'Burger',   'file' => 'burger.png',   'unlocked' => true],
            'cilek'    => ['title' => 'Çilek',    'file' => 'çilek.png',    'unlocked' => true],
            'elma'     => ['title' => 'Elma',     'file' => 'elma.png',     'unlocked' => true],
            'geyik'    => ['title' => 'Geyik',    'file' => 'geyik.png',    'unlocked' => true],
            'jake'     => ['title' => 'Jake',     'file' => 'jake.png',     'unlocked' => true],
            'kedi'     => ['title' => 'Kedi',     'file' => 'kedi.png',     'unlocked' => true],
            'kitap'    => ['title' => 'Kitap',    'file' => 'kitap.png',    'unlocked' => true],
            'kruvasan' => ['title' => 'Kruvasan', 'file' => 'kruvasan.png', 'unlocked' => true],
            'maymun'   => ['title' => 'Maymun',   'file' => 'maymun.png',   'unlocked' => true],
            'tama'     => ['title' => 'Tama',     'file' => 'tama.png',     'unlocked' => true],
            'usagi'    => ['title' => 'Usagi',    'file' => 'usagi.png',    'unlocked' => true],
            'yengec'   => ['title' => 'Yengeç',   'file' => 'yengeç.png',   'unlocked' => true],
            'yonca'    => ['title' => 'Yonca',    'file' => 'yonca.png',    'unlocked' => true],
        ];

        return view('board', compact('user', 'board', 'achievements'));
    }

    // Kaydetme Endpoint'i (AJAX / Fetch)
    public function save(Request $request, User $user)
    {
        // Yetki kontrolü: Sadece pano sahibi veya ziyaretçi kendi notunu ekliyorsa
        $isOwner = auth()->check() && auth()->id() === $user->id;

        $board = UserBoard::firstOrCreate(['user_id' => $user->id]);

        $boardItems = $request->input('board_items', []);
        $hookSlots = $request->input('hook_slots', $board->hook_slots ?? array_fill(0, 9, null));

        // Eğer ziyaretçiyse sadece board_items'a yeni postit ekleyebilir, kancaları değiştiremez
        if (!$isOwner) {
            $hookSlots = $board->hook_slots;
        }

        $board->update([
            'board_items' => $boardItems,
            'hook_slots'  => $hookSlots,
        ]);

        return response()->json(['success' => true]);
    }
}