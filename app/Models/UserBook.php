<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ReviewLike;
use App\Models\User;

class UserBook extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'rating',
        'review',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class, "book_id");
    }
    // --- BEĞENİ İLİŞKİSİ ---
    public function likes()
    {
        return $this->hasMany(ReviewLike::class, 'review_id');
    }

    // --- KULLANICI BEĞENDİ Mİ KONTROLÜ ---
    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
