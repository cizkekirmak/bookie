<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\friendship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function searchUsers(Request $request) 
    {
        $query = trim($request->get('q', ''));
        $currentUserId = auth()->id();

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('id', '!=', $currentUserId)
            ->where('username', 'LIKE', "%{$query}%")
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
                'id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'status' => $status,
                'is_sender' => $isSender,
            ];
        });

        return response()->json($result);
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6'
        ], [
            "username.required" => "where is your username?",
            "username.min" => "username must be at least 3 characters",
            "username.max" => "username cannot exceed 10 characters",
            "email.required" => "email is required",
            "email.email" => "email must be a valid email address",
            "email.unique" => "email is already taken",
            "password.required" => "password is required",
            "password.min" => "password must be at least 6 characters"
        ]);

        $incomingFields["password"] = bcrypt($incomingFields["password"]);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect("/dashboard");
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            "loginname" => "required",
            "password" => "required"
        ], [
            "loginname.required" => "username or email is required",
            "password.required" => "password is required"
        ]);

        $fieldType = filter_var($incomingFields["loginname"], FILTER_VALIDATE_EMAIL) ? "email" : "username";
        $incomingFields = [$fieldType => $incomingFields["loginname"], "password" => $incomingFields["password"]];

        if (Auth::attempt($incomingFields)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'loginname' => 'are u sure these are correct?',
        ]);
    }
}