<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftRequest extends Model
{
    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }
}
