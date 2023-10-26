<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $table ='shifts';
    protected $fillable = [
      'name',
      'schedule_in',
      'schedule_out',
        'break_start',
        'break_end'
    ];
    public function Schedule(): HasMany
    {
        return $this->hasMany(Schedules::class, 'schedule_id');
    }

    public function Shift(): HasMany
    {
        return $this->hasMany(Schedules::class, 'shift_id');
    }
}
