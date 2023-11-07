<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovedRequest extends Model
{
    protected $table = 'approved_requests';
    protected $fillable = [
        'user_id',
        'shift_request_id',
        'leave_request_id',
        'attendance_request_id',
        'status',
        'reward_flag'
    ];


    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }

    public function attendance_request(): BelongsTo
    {
        return $this->belongsTo(AttendanceRequest::class);
    }
}
