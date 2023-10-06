<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $table = 'verification';
    protected $fillable = [
        'email',
        'otp',
        'token',
        'expired_at'
    ];
}
