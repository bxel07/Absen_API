<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = ['task_member_id', 'user_id', 'name', 'project_title', 'deadline', 'description', 'reward_point', 'status'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function taskMembers(): BelongsTo
    {
        return $this->belongsTo(Task_Member::class, 'task_member_id', 'user_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }
}
