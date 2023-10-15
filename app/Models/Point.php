<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $table = 'points';

    protected $fillable = [
        'user_id',
        'main_points',
        'reward_points'
    ];
}
