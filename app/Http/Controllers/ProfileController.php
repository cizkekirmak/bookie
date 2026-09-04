<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\UserBook;
use App\Models\User;
use App\Models\friendship;

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

        $readingGoal = DB::table('reading_goals')
            ->where('user_id', $user->id)
            ->where('year', $currentYear)
            ->first();

        $readThisYear = DB::table('user_books')
            ->where('user_id', $user->id)
            ->where('status', 'read')
            ->whereYear('updated_at', $currentYear)
            ->count();

        $goalTarget = $readingGoal ? $readingGoal->target_books : null;
        $goalProgress = $goalTarget ? min(100, round(($readThisYear / $goalTarget) * 100)) : 0;

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
            "currentYear"
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // 5MB limit
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $cloudName = env('CLOUDINARY_CLOUD_NAME', 'fxyz37re');
            $apiKey    = env('CLOUDINARY_API_KEY', '899324875961436');
            $apiSecret = env('CLOUDINARY_API_SECRET', '8z8E_0aF6fG7Q68bCq48b9v2NqI');

            $timestamp = time();
            $folder    = 'bookie_avatars';

            // Cloudinary imza (Signature) hesabı
            $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
            $signature    = sha1($paramsToSign . $apiSecret);

            try {
                $response = Http::withoutVerifying()
                    ->asMultipart()
                    ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                        [
                            'name'     => 'file',
                            'contents' => fopen($file->getRealPath(), 'r'),
                            'filename' => $file->getClientOriginalName()
                        ],
                        [
                            'name'     => 'api_key',
                            'contents' => $apiKey
                        ],
                        [
                            'name'     => 'timestamp',
                            'contents' => (string)$timestamp
                        ],
                        [
                            'name'     => 'folder',
                            'contents' => $folder
                        ],
                        [
                            'name'     => 'signature',
                            'contents' => $signature
                        ],
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