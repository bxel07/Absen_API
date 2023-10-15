<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job_Level extends Model
{
    protected $table = 'job_levels';

    protected $fillable = [
      'name'
    ];

    public function department(): HasMany
    {
        return $this->hasMany('Departments');
    }
}
