<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\FriendRequestRejected;
use App\Notifications\FriendRequestAccepted;
use App\Models\friendship;
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

        $friendship = friendship::where(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $authId)->where('friend_id', $targetId);
        })->orWhere(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $targetId)->where('friend_id', $authId);
        })->first();

        if (!$friendship) {
            friendship::create([
                'user_id'   => $authId,
                'friend_id' => $targetId,
                'status'    => 'pending',
            ]);
        } elseif ($friendship->status === 'pending' && $friendship->user_id === $authId) {
            $friendship->delete();
        }

        return back();
    }

    // İsteği Kabul Et
    public function acceptRequest($id)
    {
        $authId = auth()->id();
        $targetId = (int)$id;

        // Bize gelen bekleyen isteği bul ve onayla
        $friendship = friendship::where('user_id', $targetId)
            ->where('friend_id', $authId)
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->update(['status' => 'accepted']);

            $sender = User::find($targetId);
            if ($sender) {
                $sender->notify(new FriendRequestAccepted(auth()->user()));
            }
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(["success" => true]);
        }

        return back();
    }

    // İsteği Reddet
    public function rejectRequest($id)
    {
        $authId = auth()->id();
        $targetId = (int)$id;

        $friendship = friendship::where('user_id', $targetId)
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

        $friendship = friendship::where(function ($q) use ($authId, $targetId) {
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