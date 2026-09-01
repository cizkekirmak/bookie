<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\UserBook;
use App\Models\User;
use App\Models\friendship;

class ProfileController extends Controller
{
    public function show($id = null) {
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

        return view("profile", compact("user", "userBooks", "booksCount", "friendsCount", "pendingRequests", "friendship", "isOwnProfile"));
    }

    public function settings() {
        $user = auth()->user();
        return view("ayarlar", compact("user"));
    }

    public function updateProfile(Request $request) {
        $request->validate([
            'bio' => 'nullable|string|max:160',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // 5MB limit
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            // Görseli Cloudinary'ye yükle ve dönen güvenli URL'yi kaydet
            $uploadedFile = Cloudinary::upload($request->file('avatar')->getRealPath(), [
                'folder' => 'bookie_avatars'
            ]);
            $user->avatar = $uploadedFile->getSecurePath();
        }

        $user->bio = $request->input('bio');
        $user->save();

        return redirect()->back()->with('success', 'Profil güncellendi.');
    }

    public function bulkRemoveBooks(Request $request) {
        $ids = $request->input('selected_books', []);

        if (!empty($ids)) {
            UserBook::whereIn('id', $ids)
                ->where('user_id', auth()->id())
                ->delete();

            return redirect()->back()->with('success', count($ids) . ' kitap kitaplığından kaldırıldı! 🗑️');
        }

        return redirect()->back();
    }
}