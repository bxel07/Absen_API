<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftRequest extends Model
{
    protected $table = 'shift_requests';
    protected $fillable = [
        'user_id',
        'shift',
        'shift_type',
        'reason',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }
}
