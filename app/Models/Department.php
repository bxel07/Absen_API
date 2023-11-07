<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'job_position_id',
        'job_level_id',
        'name'

    ];

    public function employment(): HasMany
    {
        return $this->hasMany(Employment::class);
    }

    public function jobLevel(): BelongsTo
    {
        return $this->belongsTo('job_levels', 'job_level_id');
    }
    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(Job_Position::class);
    }

}
