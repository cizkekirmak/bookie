<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\FriendRequestRejected;
use App\Notifications\FriendRequestAccepted;
use App\Models\Friendship;
use App\Models\User;

class FriendshipController extends Controller
{
    // Arkadaşlık İsteği Gönder / İptal Et
    public function sendRequest($id)
    {
        $authId = auth()->id();
        $targetId = (int)$id;

        if ($authId === $targetId) {
            return back();
        }

        // Önceden var olan ilişkiyi ara
        $friendship = Friendship::where(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $authId)->where('friend_id', $targetId);
        })->orWhere(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $targetId)->where('friend_id', $authId);
        })->first();

        if (!$friendship) {
            // Hiç istek yoksa yeni istek oluştur
            Friendship::create([
                'user_id' => $authId,
                'friend_id' => $targetId,
                'status' => 'pending',
            ]);
        } elseif ($friendship->status === 'pending' && $friendship->user_id === $authId) {
            // Biz istek atmışsak ve bekliyorsa, butona tekrar basıldığında isteği iptal et
            $friendship->delete();
        }

        return back();
    }

    // İsteği Kabul Et
public function acceptRequest($id)
{
    $authId = auth()->id();
    $targetId = (int)$id;

    // 1. İsteği atan kullanıcıyı doğrudan bul
    $sender = \App\Models\User::find($targetId);

    // 2. Bildirimi zorla gönder
    if ($sender) {
        $sender->notify(new \App\Notifications\FriendRequestAccepted(auth()->user()));
    }

    // 3. Bildirim tablosuna yazıp yazmadığını ekranda hemen gör
    dd([
        'Bulunan Kullanıcı' => $sender ? $sender->toArray() : 'Kullanıcı bulunamadı!',
        'Son Eklenen Bildirim' => \DB::table('notifications')->latest()->first()
    ]);
}

    public function rejectRequest($id)
    {
        $authId = auth()->id();
        $targetId = (int)$id;

        $friendship = Friendship::where('user_id', $targetId)
            ->where('friend_id', $authId)
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->delete();

            $sender = User::find($targetId);
            if ($sender) {
                $sender->notify(new FriendRequestRejected(auth()->user()));
            }
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(["success" => true]);
        } 

        return back();
    }

    // Arkadaşlıktan Çıkar
    public function removeFriend($id)
    {
        $authId = auth()->id();
        $targetId = (int)$id;

        $friendship = Friendship::where(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $authId)->where('friend_id', $targetId);
        })->orWhere(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $targetId)->where('friend_id', $authId);
        })->first();

        if ($friendship) {
            $friendship->delete();
        }
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(["success" => true]);
        }

        return back();
    }
}