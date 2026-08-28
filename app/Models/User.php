<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

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
        $this->notify(new ResetPasswordNotification($token));
    }
    public function books()
    {
    return $this->hasMany(\App\Models\Book::class);
    }

    public function friends() {
        $friendsOfMine = $this->belongsToMany(User::class, "friendships", "user_id", "friend_id")
            ->wherePivot('status', "accepted");
        $friendOf = $this->belongsToMany(User::class, "friendships", "friend_id", "user_id")
            ->wherePivot('status', "accepted");
        return $friendsOfMine->get()->merge($friendOf->get());
    }

    public function pendingFriendRequests() {
        return $this->belongsToMany(User::class, "friendships", "friend_id", "user_id")
            ->wherePivot('status', 'pending');
    }

    public function friendshipStatusWith(User $user) {
        $friendship = \App\Models\Friendship::where(function($q) use ($user) {
            $q->where('user_id', $this->id)->where("friend_id", $user->id);
        })->orWhere(function($q) use ($user) {
            $q->where('user_id', $user->id)->where("friend_id", $this->id);
        })->first();

        return $friendship ? $friendship->status : "none" ; 
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
            'name'   => "adminin title'a ihtiyacı yok",
            'icon'   => '😎',
            'bg'     => 'transparent', // veya istediğin özel renk
            'border' => 'transparent',
            'color'  => '#ffffff',
            'is_admin' => true,
        ];
    }

    $count = $this->finishedBooksCount();

    return match (true) {
        $count >= 100 => ['name' => 'bookie admini olmaya yakın', 'icon' => '👑', 'bg' => '#fff4d6', 'border' => '#e0b84c', 'color' => '#8d5b12'],
        $count >= 50  => ['name' => 'lol oynamayı bıraktım',      'icon' => '🔮', 'bg' => '#f5e8ff', 'border' => '#b382d9', 'color' => '#5e2a84'],
        $count >= 25  => ['name' => 'kitap.sevdası34',            'icon' => '📜', 'bg' => '#faede0', 'border' => '#c2a178', 'color' => '#574127'],
        $count >= 10  => ['name' => 'kitaplık kedisi',           'icon' => '🐾', 'bg' => '#e6f4dc', 'border' => '#7bb35c', 'color' => '#1a3c11'],
        $count >= 5   => ['name' => 'meraklı minik',              'icon' => '🕯️', 'bg' => '#e1effa', 'border' => '#6ba2cc', 'color' => '#1f3c5a'],
        $count >= 3   => ['name' => 'yeni başlamış',              'icon' => '☕', 'bg' => '#fceddf', 'border' => '#cca17a', 'color' => '#633d1b'],
        default       => ['name' => 'gariban üye',                'icon' => '🌱', 'bg' => '#eef7ea', 'border' => '#88b877', 'color' => '#2e5922'],
    };
} public static function allTitles(): array
    {
        return [
            ['req' => 0,   'name' => 'gariban üye',                 'icon' => '🌱', 'desc' => 'elinden geleni yapıyor..'],
            ['req' => 3,   'name' => 'yeni başlamış',               'icon' => '☕', 'desc' => 'okuduklarını beğendin mi?'],
            ['req' => 5,   'name' => 'meraklı minik',               'icon' => '🕯️', 'desc' => 'trnin %99undan fazla okudun !!'],
            ['req' => 10,  'name' => 'kitaplık kedisi',            'icon' => '🐾', 'desc' => 'miyav miyav'],
            ['req' => 25,  'name' => 'kitap.sevdası43',             'icon' => '📜', 'desc' => 'kütayhalılara özel'],
            ['req' => 50,  'name' => 'lol oynamayı bıraktım',       'icon' => '🔮', 'desc' => 'piramidin en altından çıkış'],
            ['req' => 100, 'name' => 'bookie admini olmaya yakın',  'icon' => '👑', 'desc' => 'mail atarsanız yardımcı olurum..'],
        ];
    }
}


