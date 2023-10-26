<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job_Position extends Model
{
    protected $table = 'job_positions';

    protected $fillable = [
        'name'
    ];

    public function department(): HasMany
    {
        return $this->hasMany('Departments');
    }

}
