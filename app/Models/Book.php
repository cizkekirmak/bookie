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
        'page_count',
        'gutenberg_id',
        'download_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes() 
    {
        return $this->hasMany(\App\Models\ReviewLike::class, "review_id");
    }

    public function isLikedBy(?User $user): bool 
    {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Google Books kapak linklerini yüksek çözünürlüğe (HD) optimize eder
     */
    protected function enhanceCoverUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (str_contains($url, 'books.google.com') || str_contains($url, 'books.googleusercontent.com')) {
            $url = str_replace('http://', 'https://', $url);
            $url = str_replace('&edge=curl', '', $url);
            $url = preg_replace('/zoom=[1-5]/', 'zoom=2', $url);
        }

        return $url;
    }

    /**
     * $book->cover_image çağrıldığında netleştirilmiş URL döner
     */
    public function getCoverImageAttribute($value)
    {
        return $this->enhanceCoverUrl($value);
    }

    /**
     * $book->cover_url çağrıldığında varsayılan görsel ya da netleştirilmiş URL döner
     */
    public function getCoverUrlAttribute()
    {
        $rawCover = $this->attributes['cover_image'] ?? ($this->attributes['cover_url'] ?? null);

        if (empty($rawCover)) {
            return asset('images/default-cover.jpg');
        }

        return $this->enhanceCoverUrl($rawCover);
    }
}