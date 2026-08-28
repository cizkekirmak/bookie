<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function getFriends()
    {
        $user = Auth::user();
        $friendsList = $user->friends();

        $data = [];
        foreach ($friendsList as $friend) {
            // Bu arkadaşla aramızdaki en son mesajın tarihi
            $lastMessage = Message::where(function ($q) use ($user, $friend) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $friend->id);
                })
                ->orWhere(function ($q) use ($user, $friend) {
                    $q->where('sender_id', $friend->id)->where('receiver_id', $user->id);
                })
                ->latest()
                ->first();

            // Bu arkadaştan gelen okunmamış mesaj sayısı
            $unreadCount = Message::where('sender_id', $friend->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            // Avatar belirleme
            if ($friend->avatar) {
                if (str_starts_with($friend->avatar, 'http')) {
                    $avatar = $friend->avatar;
                } else {
                    $avatar = asset('storage/' . ltrim($friend->avatar, '/'));
                }
            } else {
                $avatar = asset('images/profile.jpg');
            }

            $data[] = [
                'id' => $friend->id,
                'username' => $friend->username ?? $friend->name ?? 'Kullanıcı',
                'avatar' => $avatar,
                'unread_count' => $unreadCount,
                'last_interaction' => $lastMessage ? $lastMessage->created_at->timestamp : 0,
            ];
        }

        // En son konuşulan kişiyi en üste alacak şekilde sırala
        usort($data, function ($a, $b) {
            return $b['last_interaction'] <=> $a['last_interaction'];
        });

        return response()->json($data);
    }
    // 2. Mesaj Geçmişi
    public function getMessages($friendId)
    {
        try {
            $user = Auth::user();

            // Okundu olarak işaretle
            Message::where('sender_id', $friendId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // Mesajları çek
            $messages = Message::where(function ($q) use ($user, $friendId) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $friendId);
                })
                ->orWhere(function ($q) use ($user, $friendId) {
                    $q->where('sender_id', $friendId)->where('receiver_id', $user->id);
                })
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($msg) use ($user) {
                    return [
                        'id' => $msg->id,
                        'is_mine' => (int)$msg->sender_id === (int)$user->id,
                        'message' => $msg->message,
                        'time' => $msg->created_at ? $msg->created_at->format('H:i') : '',
                    ];
                });

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 3. Mesaj Gönder
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required',
                'message' => 'required|string|max:1000',
            ]);

            $user = Auth::user();

            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => (int) $request->receiver_id,
                'message' => $request->message,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'is_mine' => true,
                    'message' => $message->message,
                    'time' => $message->created_at ? $message->created_at->format('H:i') : date('H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 4. Okunmamış Sayısı
    public function getUnreadCount()
    {
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}