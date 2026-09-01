<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\DB;
use App\Notifications\FriendRequestRejected;
use App\Notifications\FriendRequestAccepted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Models\friendship;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminRecommendationController;

/*
|--------------------------------------------------------------------------
| Kullanıcı Arama API
|--------------------------------------------------------------------------
*/
Route::get('/api/search-users', function (Request $request) {
    $query = $request->input("q");
    $authId = auth()->id();

    if (empty($query) || strlen(trim($query)) < 1) {
        return response()->json([]);
    }

    $users = User::where("id", "!=", $authId)
        ->where(function ($q) use ($query) {
            $q->where("username", "LIKE", "%{$query}%");
        })
        ->take(5)
        ->get()
        ->map(function ($targetUser) use ($authId) {
            $friendship = friendship::where(function ($q) use ($authId, $targetUser) {
                $q->where("user_id", $authId)->where("friend_id", $targetUser->id);
            })->orWhere(function ($q) use ($authId, $targetUser) {
                $q->where("user_id", $targetUser->id)->where("friend_id", $authId);
            })->first();

            $status = $friendship ? $friendship->status : "none";

            return [
                "id"        => $targetUser->id,
                "username"  => $targetUser->username,
                "avatar"    => $targetUser->avatar, // <-- İŞTE EKSİK OLAN SATIR
                "status"    => $status,
                "is_sender" => $friendship ? ($friendship->user_id == $authId) : false,
            ];
        });

    return response()->json($users);
})->middleware("auth");

/*
|--------------------------------------------------------------------------
| Giriş, Kayıt ve Genel Sayfalar
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Şifre Sıfırlama
|--------------------------------------------------------------------------
*/
Route::get('/forgotpassword', function () {
    return view('forgotpassword');
})->name('password.request');

Route::post('/forgotpassword', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
    ? back()->with('status', __('check your email !!(its probably in the junk folder)'))
    : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', 'your new password is ready!')
        : back()->withErrors(['email' => __($status)]);
})->name('password.update');

/*
|--------------------------------------------------------------------------
| Kimlik Doğrulaması Gerektiren Rotalar (Auth Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [BookController::class, 'index'])->name('dashboard');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get("/books/{key}", [BookController::class, 'show'])->name('show');
    Route::post("/books/{key}/save", [BookController::class, 'saveOrUpdate'])->name('books.save');
    Route::post("/books/{book}/reviews", [ReviewController::class, "store"])->name("reviews.store");
    Route::post('/reviews/{review}/toggle-like', [ReviewController::class, 'toggleLike'])->name('reviews.toggleLike');
    Route::get('/api/search-books', [BookController::class, 'searchApi'])->name('api.books.search');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/books/bulk-remove', [ProfileController::class, 'bulkRemoveBooks'])->name('profile.books.bulkRemove');

    Route::get('/messages/friends', [MessageController::class, 'getFriends']);
    Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount']);
    Route::get('/messages/{friendId}', [MessageController::class, 'getMessages']);
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);


    Route::get('/ayarlar', [ProfileController::class, 'settings'])->name('ayarlar');
    Route::post('/ayarlar', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/{id?}', [ProfileController::class, 'show'])->name('profile');

    Route::post("/friends/{id}/request", function ($id) {
        $authId = auth()->id();
        $targetId = (int)$id;

        if ($authId === $targetId) return back();

        $existing = friendship::where(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $authId)->where('friend_id', $targetId);
        })->orWhere(function ($q) use ($authId, $targetId) {
            $q->where('user_id', $targetId)->where('friend_id', $authId);
        })->first();

        if ($existing) {
            // İstek pending aşamasındaysa ve biz atmışsak isteği iptal et
            if ($existing->status === 'pending' && $existing->user_id === $authId) {
                $existing->delete();
            }
        } else {
            friendship::create([
                'user_id' => $authId,
                'friend_id' => $targetId,
                'status' => 'pending'
            ]);
        }

        return back();
    })->name('friends.request');

    Route::post('/friends/{id}/accept', function ($id) {
    $authId = auth()->id();
    $targetId = (int)$id;

    \App\Models\friendship::where(function($q) use ($authId, $targetId) {
        $q->where('user_id', $targetId)->where('friend_id', $authId);
    })->orWhere(function($q) use ($authId, $targetId) {
        $q->where('user_id', $authId)->where('friend_id', $targetId);
    })->update(['status' => 'accepted']);

    $sender = \App\Models\User::find($targetId);
    $currentUser = auth()->user();

    if ($sender) {
        $sender->notify(new \App\Notifications\FriendRequestAccepted($currentUser));
    }

    \Illuminate\Support\Facades\DB::table('notifications')->insert([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\FriendRequestAcceptedSelf',
        'notifiable_type' => 'App\Models\User',
        'notifiable_id' => $authId,
        'data' => json_encode([
            'type' => 'accepted_self',
            'sender_id' => $sender->id ?? $targetId,
            'sender_name' => $sender->name ?? $sender->username ?? 'Kullanıcı',
            'message' => 'arkadaşlık isteğini kabul ettin. Artık arkadaşsınız!',
        ]),
        'read_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Accepted'
    ]);
})->name("friends.accept");

Route::post('/friends/{id}/reject', function ($id) {
    $authId = auth()->id();
    $targetId = (int)$id;

    $deleted = Friendship::where('user_id', $targetId)
        ->where('friend_id', $authId)
        ->where('status', 'pending')
        ->delete();

    if ($deleted) {
        $sender = User::find($targetId);
        if ($sender) {
            $sender->notify(new FriendRequestRejected(auth()->user()));
        }
    }

    if (request()->ajax() || request()->wantsJson()) {
        return response()->json(['success' => true]);
    }

    return back();
})->name("friends.reject");

Route::post('/friends/{id}/remove', function ($id) {
    $authId = auth()->id();
    $targetId = (int)$id;

    friendship::where(function($q) use ($authId, $targetId) {
        $q->where('user_id', $authId)->where('friend_id', $targetId);
    })->orWhere(function($q) use ($authId, $targetId) {
        $q->where('user_id', $targetId)->where('friend_id', $authId);
    })->delete();

    if (request()->ajax() || request()->wantsJson()) {
        return response()->json(['success' => true]);
    }

    return back();
})->name("friends.remove");
    Route::get('/adminRecommendation', [AdminRecommendationController::class, "index"])->name('adminRecommendation');
    Route::post('/adminRecommendation', [AdminRecommendationController::class, "store"])->name('adminRecommendation.store');
});


Route::post('/notifications/mark-as-read', function () {
    try {
        $userId = auth()->id();

        if ($userId) {
            DB::table('notifications')
                ->where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
})->name('notifications.markRead');

Route::post("/notifications/clear-all", function() {
    auth()->user()->notifications()->delete();
    return response()->json(["success" => true]);
})->name("notifications.clearAll");

Route::get("/notifications/unread-count", function() {
    $user = auth()->user();
    $pendingList = $user->pendingFriendRequests()->with('sender')->get();
    $notifications = $user->notifications;
    $totalCount = $pendingList->count() + $user->unreadNotifications()->count();

    $html = view("partials.notifications-items", compact("pendingList", "notifications", "totalCount"))->render();

    return response()->json([
        "total" => $totalCount,
        "has_unread" => $totalCount > 0,
        "html" => $html
    ]);
})->middleware("auth")->name("notifications.unreadCount");

Route::get('/api/random-book-recommendation', [BookController::class, 'getRandomRecommendation']);
Route::get('/fix-render', function () {
    // 1. Storage linkini oluşturur
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    
    // 2. Cache'i temizler ki backend eksik kapakları diske tekrar indirsin
    \Illuminate\Support\Facades\Cache::flush();
    
    return 'Bağlantılar ve önbellek yenilendi! Sayfayı yenileyebilirsin.';
});
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['tr', 'en'])) {
        session()->put('locale', $locale);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');