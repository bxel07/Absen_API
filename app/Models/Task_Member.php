<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task_Member extends Model
{
    protected $fillable = ['user_id'];
    protected $table ='task_members';

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'task_member_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'task_member_id', 'user_id');
    }
}
