<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\ReviewLike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'review',
        'rating',
        'cover_image',
        'google_book_id',
        'genre',
        'publication_date',
        'open_library_key',
        'status',
        'cover_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function likes() {
        return $this->hasMany(\App\Models\ReviewLike::class, "review_id");
    }

    public function isLikedBy(?User $user): bool {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getCoverUrlAttribute()
    {
        if (empty($this->cover_image)) {
            return asset('images/default-cover.jpg');
        }

        return $this->cover_image;
    }
}
