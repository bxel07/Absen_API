<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskMember extends Model
{
    protected $table = 'task_members';
    protected $fillable = ['user_id'];

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'task_member_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'task_member_id', 'user_id');
    }
}
