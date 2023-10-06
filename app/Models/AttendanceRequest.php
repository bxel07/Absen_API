<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'shift',
        'clock_in',
        'clock_out',
        'description',
        'upload_file',
        'point'

    ];

    protected $casts = [
        'location' => 'Geometry'
    ];


    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }
}
