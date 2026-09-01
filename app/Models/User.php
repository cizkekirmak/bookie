<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Fillable(['username', 'email', 'password', 'avatar', 'bio'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $htmlContent = '
        <div style="background-color: #f4f7f4; padding: 30px; font-family: sans-serif;">
            <div style="max-width: 500px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; border: 1px solid #e0e0e0;">
                <h2 style="color: #2d5a27; margin-top: 0;">' . e(__('haiii !!')) . '</h2>
                <p style="color: #444; font-size: 15px; line-height: 1.5;">' . e(__('we heard that you want to reset your password :( dont worry i got u, here is your link')) . '</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $url . '" style="background-color: #2d5a27; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 15px;">' . e(__('reset password')) . '</a>
                </div>
                <p style="color: #666; font-size: 14px;">' . e(__('this link will die in 60 minutes, be quick !')) . '</p>
                <p style="color: #666; font-size: 14px;">' . e(__('if u didnt request this link, just ignore it :p')) . '</p>
                <p style="color: #2d5a27; font-size: 14px; margin-top: 25px; white-space: pre-line; font-weight: bold;">' . e(__('with love,\nBookie Team')) . '</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
                <p style="color: #999; font-size: 12px; word-break: break-all;">
                    ' . e(__('If you\'re having trouble clicking the "reset password" button, copy and paste the URL below into your web browser:')) . '<br>
                    <a href="' . $url . '" style="color: #2d5a27;">' . $url . '</a>
                </p>
            </div>
        </div>';

        $response = Http::withHeaders([
            'api-key' => env('BREVO_KEY'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => env('MAIL_FROM_NAME', 'Bookie'),
                'email' => env('MAIL_FROM_ADDRESS', 'bookieapp.info@gmail.com'),
            ],
            'to' => [
                ['email' => $this->getEmailForPasswordReset()]
            ],
            'subject' => __('Bookie - Reset Password Notification'),
            'htmlContent' => $htmlContent,
        ]);

        if (!$response->successful()) {
            Log::error('Brevo API Hatası: ' . $response->body());
        }
    }

    public function books()
    {
        return $this->hasMany(\App\Models\Book::class);
    }

    public function friends() 
    {
        $friendsOfMine = $this->belongsToMany(User::class, "friendships", "user_id", "friend_id")
            ->wherePivot('status', "accepted");
        $friendOf = $this->belongsToMany(User::class, "friendships", "friend_id", "user_id")
            ->wherePivot('status', "accepted");
        return $friendsOfMine->get()->merge($friendOf->get());
    }

    public function pendingFriendRequests() 
    {
        return $this->belongsToMany(User::class, "friendships", "friend_id", "user_id")
            ->wherePivot('status', 'pending');
    }

    public function friendshipStatusWith(User $user) 
    {
        $friendship = \App\Models\Friendship::where(function($q) use ($user) {
            $q->where('user_id', $this->id)->where("friend_id", $user->id);
        })->orWhere(function($q) use ($user) {
            $q->where('user_id', $user->id)->where("friend_id", $this->id);
        })->first();

        return $friendship ? $friendship->status : "none"; 
    }

    public function finishedBooksCount(): int
    {
        return \App\Models\UserBook::where('user_id', $this->id)
            ->where('status', 'read')
            ->count();
    }

    public function getReadingTitleAttribute(): array 
    {
        if ($this->is_admin || $this->role === 'admin' || $this->username === 'admin' || $this->email === 'bookieapp.info@gmail.com') {
            return [
                'name'     => "admin doesn't need a title",
                'icon'     => '😎',
                'bg'       => 'transparent',
                'border'   => 'transparent',
                'color'    => '#ffffff',
                'is_admin' => true,
            ];
        }

        $count = $this->finishedBooksCount();

        return match (true) {
            $count >= 100 => ['name' => 'almost a bookie admin', 'icon' => '👑', 'bg' => '#fff4d6', 'border' => '#e0b84c', 'color' => '#8d5b12'],
            $count >= 50  => ['name' => 'touched grass',          'icon' => '🔮', 'bg' => '#f5e8ff', 'border' => '#b382d9', 'color' => '#5e2a84'],
            $count >= 25  => ['name' => 'xX_bookieLover67_Xx',    'icon' => '📜', 'bg' => '#faede0', 'border' => '#c2a178', 'color' => '#574127'],
            $count >= 10  => ['name' => 'bookshelf cat',          'icon' => '🐾', 'bg' => '#e6f4dc', 'border' => '#7bb35c', 'color' => '#1a3c11'],
            $count >= 5   => ['name' => 'curious worm',            'icon' => '🕯️', 'bg' => '#e1effa', 'border' => '#6ba2cc', 'color' => '#1f3c5a'],
            $count >= 3   => ['name' => 'just getting started',   'icon' => '☕', 'bg' => '#fceddf', 'border' => '#cca17a', 'color' => '#633d1b'],
            default       => ['name' => 'certified noob',         'icon' => '🌱', 'bg' => '#eef7ea', 'border' => '#88b877', 'color' => '#2e5922'],
        };
    }

    public static function allTitles(): array
    {
        return [
            ['req' => 0,   'name' => 'certified noob',         'icon' => '🌱', 'desc' => 'doing their best..'],
            ['req' => 3,   'name' => 'just getting started',   'icon' => '☕', 'desc' => 'did you like what u read?'],
            ['req' => 5,   'name' => 'curious worm',           'icon' => '🕯️', 'desc' => 'alreading reading more than average'],
            ['req' => 10,  'name' => 'bookshelf cat',          'icon' => '🐾', 'desc' => 'meow meow'],
            ['req' => 25,  'name' => 'xX_bookieLover67_Xx',    'icon' => '📜', 'desc' => 'sorry for being so edgy'],
            ['req' => 50,  'name' => 'touched grass',          'icon' => '🔮', 'desc' => 'escaping the bottom of the pyramid'],
            ['req' => 100, 'name' => 'almost a bookie admin',  'icon' => '👑', 'desc' => 'shoot me an email, i might help..'],
        ];
    }
}