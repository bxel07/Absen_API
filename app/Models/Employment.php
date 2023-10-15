<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employment extends Model
{

    protected $table = 'employments';

    protected $fillable =[
      'user_id',
      'company_id',
      'branch_id',
      'department_id',
      'join_date',
      'end_date'
    ];
    public function users(): BelongsTo
    {
        return $this->belongsTo('users', 'user_id');
    }

    public function companies(): BelongsTo
    {
        return $this->belongsTo('companies', 'company_id');
    }

    public function departments(): BelongsTo
    {
        return $this->belongsTo('departments', 'department_id');
    }

    public function branches(): BelongsTo
    {
        return $this->belongsTo('branches', 'branch_id');
    }
}
