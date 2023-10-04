<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
    protected $fillable = [
        'schedule_id',
        'shift_id',
        'user_id',
        'clock_in',
        'clock_out',
        'photo',
        'shift_schedule',
        'shift',
        'location',
        'notes',
    ];

    protected $casts = [
        'location' => 'Geometry'
    ];

}
