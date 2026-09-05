<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\friendship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Kullanıcı arama API'si (Büyük/küçük harf ve Türkçe karakter duyarsız)
     */
    public function searchUsers(Request $request) 
    {
        $rawQuery = trim($request->get('q', ''));
        $currentUserId = auth()->id();

        if (mb_strlen($rawQuery) < 2) {
            return response()->json([]);
        }

        // Türkçe uyumlu küçük harfe dönüştür: I -> ı, İ -> i
        $cleanQuery = mb_convert_case($rawQuery, MB_CASE_LOWER, 'UTF-8');

        // SQL seviyesinde büyük/küçük harf ayrımını kaldırarak ara
        $users = User::where('id', '!=', $currentUserId)
            ->whereRaw("LOWER(REPLACE(REPLACE(username, 'I', 'ı'), 'İ', 'i')) LIKE ?", ["%{$cleanQuery}%"])
            ->select('id', 'username', 'avatar')
            ->limit(8)
            ->get();

        $result = $users->map(function ($user) use ($currentUserId) {
            $friendship = friendship::where(function ($q) use ($user, $currentUserId) {
                $q->where('user_id', $currentUserId)->where('friend_id', $user->id);
            })->orWhere(function ($q) use ($user, $currentUserId) {
                $q->where('user_id', $user->id)->where('friend_id', $currentUserId);
            })->first();

            $status = 'none';
            $isSender = false;

            if ($friendship) {
                $status = $friendship->status;
                $isSender = ($friendship->user_id == $currentUserId);
            }

            return [
                'id'        => $user->id,
                'username'  => $user->username, // Orijinal yazımı korur
                'avatar'    => $user->avatar,
                'status'    => $status,
                'is_sender' => $isSender,
            ];
        });

        return response()->json($result);
    }

    /**
     * Kayıt işlemi (Kullanıcının harf tercihini korur, mükerrerliği engeller)
     */
    public function register(Request $request)
    {
        $rawUsername = trim($request->username ?? '');
        $cleanUsername = mb_convert_case($rawUsername, MB_CASE_LOWER, 'UTF-8');

        $incomingFields = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                // Orijinal yazımı bozmadan, veritabanındaki büyük/küçük harf eşleşmesini denetler
                Rule::unique('users', 'username')->where(function ($query) use ($cleanUsername) {
                    return $query->whereRaw("LOWER(REPLACE(REPLACE(username, 'I', 'ı'), 'İ', 'i')) = ?", [$cleanUsername]);
                }),
            ],
            'email'    => 'required|email:rfc,dns|max:255|unique:users,email',
            'password' => 'required|string|min:6'
        ], [
            "username.required" => __("where is your username?"),
            "username.min"      => __("username must be at least 3 characters"),
            "username.max"      => __("username cannot exceed 50 characters"),
            "username.unique"   => __("this username is already taken"),
            "email.required"    => __("email is required"),
            "email.email"       => __("email must be a valid email address"),
            "email.unique"      => __("email is already taken"),
            "password.required" => __("password is required"),
            "password.min"      => __("password must be at least 6 characters")
        ]);

        // Kullanıcının yazdığı orijinal büyük/küçük harf haliyle kaydet
        $incomingFields["username"] = trim($incomingFields["username"]);
        $incomingFields["email"]    = trim($incomingFields["email"]);
        $incomingFields["password"] = bcrypt($incomingFields["password"]);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect("/dashboard");
    }

    /**
     * Giriş işlemi (IRMAK, ırmak veya Irmak yazılsa da eşleştirir)
     */
    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            "loginname" => "required",
            "password"  => "required"
        ], [
            "loginname.required" => __("username or email is required"),
            "password.required"  => __("password is required")
        ]);

        $loginInput = trim($incomingFields["loginname"]);
        $isEmail    = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        // Türkçe I/ı ve İ/i uyumlu küçük harfe dönüştür
        $cleanLogin = mb_convert_case($loginInput, MB_CASE_LOWER, 'UTF-8');

        // Kullanıcıyı büyük/küçük harfe bakmaksızın eşleştir
        $user = User::where(function ($query) use ($cleanLogin, $isEmail) {
            if ($isEmail) {
                $query->whereRaw('LOWER(email) = ?', [$cleanLogin]);
            } else {
                $query->whereRaw("LOWER(REPLACE(REPLACE(username, 'I', 'ı'), 'İ', 'i')) = ?", [$cleanLogin]);
            }
        })->first();

        // Kullanıcı bulunduysa ve parola doğruysa oturum aç
        if ($user && Hash::check($incomingFields['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'loginname' => __('are u sure these are correct?'),
        ])->onlyInput('loginname');
    }
}