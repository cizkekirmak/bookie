<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'board_items',
        'hook_slots',
        'is_locked',
    ];

    protected $casts = [
        'board_items' => 'array',
        'hook_slots'  => 'array',
        'is_locked'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}