<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovedTask extends Model
{
    protected $table = 'approved_tasks';
    protected $fillable = [
        'user_id',
        'task_id',
        'status',
    ];
}
