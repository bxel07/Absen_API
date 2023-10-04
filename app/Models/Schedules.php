<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedules extends Model
{

    protected $fillable = [
        'schedule_shift',
        'effective'
    ];
    public function Schedule(): HasMany
    {
        return $this->hasMany(Shift::class, 'schedule_id');
    }

    public function Shift(): HasMany
    {
        return $this->hasMany(Shift::class, 'shift_id');
    }
}
