<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Companies extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
    ];

    public function employment(): HasMany
    {
        return $this->hasMany('employments', 'company_id');
    }
}
