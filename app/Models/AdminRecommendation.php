<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        "book_key",
        "title",
        "authors",
        "cover_url",
        "admin_note",
        "is_active",
    ];
}
