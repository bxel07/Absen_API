<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleShift extends Model
{
    protected $fillable = [
        'shift_id',
        'schedule_id',
        'user_id',
        'initial_shift'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }
}
