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
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; background-color: #c2e2a3; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #c2e2a3; padding: 40px 15px;">
                <tr>
                    <td align="center">
                        <!-- Logo -->
                        <div style="margin-bottom: 25px; text-align: center;">
                            <span style="font-family: Georgia, serif; font-size: 42px; font-weight: bold; color: #2d5a27; letter-spacing: -1px;">B<span style="font-style: italic;">oo</span>kie</span>
                        </div>

                        <!-- Card -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 570px; background-color: #ffffff; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); padding: 40px 35px; text-align: left;">
                            <tr>
                                <td>
                                    <h2 style="font-size: 19px; font-weight: bold; color: #1f2937; margin-top: 0; margin-bottom: 20px;">' . e(__('haiii !!')) . '</h2>
                                    
                                    <p style="font-size: 15px; color: #4b5563; line-height: 1.6; margin-bottom: 25px;">' . e(__('we heard that you want to reset your password :( dont worry i got u, here is your link')) . '</p>

                                    <!-- Buton -->
                                    <div style="text-align: center; margin: 30px 0;">
                                        <a href="' . $url . '" style="background-color: #24292f; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; letter-spacing: 0.3px;">' . e(__('reset password')) . '</a>
                                    </div>

                                    <p style="font-size: 15px; color: #4b5563; line-height: 1.6; margin-bottom: 12px;">' . e(__('this link will die in 60 minutes, be quick !')) . '</p>
                                    <p style="font-size: 15px; color: #4b5563; line-height: 1.6; margin-bottom: 25px;">' . e(__('if u didnt request this link, just ignore it :p')) . '</p>

                                    <p style="font-size: 15px; color: #4b5563; line-height: 1.5; margin-bottom: 30px;">' . nl2br(e(__('with love,\nBookie Team'))) . '</p>

                                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0 20px 0;">

                                    <!-- Alt Bilgi -->
                                    <p style="font-size: 12px; color: #6b7280; line-height: 1.5; margin: 0; word-break: break-all;">
                                        ' . e(__('If you\'re having trouble clicking the "reset password" button, copy and paste the URL below into your web browser:')) . '<br>
                                        <a href="' . $url . '" style="color: #2563eb; text-decoration: underline;">' . $url . '</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

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