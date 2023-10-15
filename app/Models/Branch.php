<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = [
      'company_id',
      'name'
    ];

    public function companies(): BelongsTo
    {
        return $this->belongsTo('companies', 'company_id');
    }
}
