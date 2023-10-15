<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftRequest extends Model
{
    protected $table = 'shift_requests';
    protected $fillable = [
        'user_id',
        'on_date',
        'old_shift_start',
        'old_shift_end',
        'new_shift_start',
        'new_shift_end',
        'reason',
        'delegations',
        'upload_file',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }
}
