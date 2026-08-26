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

    public function getReadingTitleAttribute(): array {
        $count = $this->finishedBooksCount();

        return match (true) {
        $count >= 30 => ['name' => 'Master of Tomes',       'icon' => '👑', 'bg' => '#fff4d6', 'border' => '#e0b84c', 'color' => '#8d5b12'],
        $count >= 20 => ['name' => 'Grand Librarian',       'icon' => '🔮', 'bg' => '#f5e8ff', 'border' => '#b382d9', 'color' => '#5e2a84'],
        $count >= 15 => ['name' => 'Castle Archivist',       'icon' => '📜', 'bg' => '#faede0', 'border' => '#c2a178', 'color' => '#574127'],
        $count >= 10 => ['name' => 'Cat Whisperer Scholar', 'icon' => '🐾', 'bg' => '#e6f4dc', 'border' => '#7bb35c', 'color' => '#1a3c11'],
        $count >= 6  => ['name' => 'Midnight Reader',        'icon' => '🕯️', 'bg' => '#e1effa', 'border' => '#6ba2cc', 'color' => '#1f3c5a'],
        $count >= 3  => ['name' => 'Cozy Page Turner',      'icon' => '☕', 'bg' => '#fceddf', 'border' => '#cca17a', 'color' => '#633d1b'],
        default      => ['name' => 'Novice Bookworm',        'icon' => '🌱', 'bg' => '#eef7ea', 'border' => '#88b877', 'color' => '#2e5922'],
    };
    }

    public static function allTitles(): array
    {
        return [
            ['req' => 0,  'name' => 'Novice Bookworm',        'icon' => '🌱', 'desc' => 'Yeni bir serüvenin ilk adımı.'],
            ['req' => 3,  'name' => 'Cozy Page Turner',      'icon' => '☕', 'desc' => 'Sıcak bir içecekle sayfaları çevirenler.'],
            ['req' => 6,  'name' => 'Midnight Reader',        'icon' => '🕯️', 'desc' => 'Gece lambası ışığında okumaya doyamayanlar.'],
            ['req' => 10, 'name' => 'Cat Whisperer Scholar', 'icon' => '🐾', 'desc' => 'Kedi mırıltısı eşliğinde 10 kitap devirenler.'],
            ['req' => 15, 'name' => 'Castle Archivist',       'icon' => '📜', 'desc' => 'Kütüphane raflarının tozunu yutanlar.'],
            ['req' => 20, 'name' => 'Grand Librarian',       'icon' => '🔮', 'desc' => 'Kitapların büyüsüne tamamen kapılanlar.'],
            ['req' => 30, 'name' => 'Master of Tomes',       'icon' => '👑', 'desc' => 'Kitaplığın mutlak efendisi.'],
        ];
    }
}


