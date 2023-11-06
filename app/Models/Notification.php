<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'user_id',
        'read_status_for_admin',
        'read_status_for_user',
    ];
}
