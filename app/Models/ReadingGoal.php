<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingGoal extends Model
{
    protected $fillable = ['user_id', 'year', 'target_books'];
}
