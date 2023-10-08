<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';
    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'start_end',
        'reason',
        'delegations',
        'upload_file',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }
}
