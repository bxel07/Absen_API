<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'job_position_id',
        'job_level_id',
        'name'

    ];

    public function jobLevel(): BelongsTo
    {
        return $this->belongsTo('job_levels', 'job_level_id');
    }
    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo('job_positions', 'job_position_id');
    }

}
