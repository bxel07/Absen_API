<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = [
        'task_member_id',
        'user_id',
        'name',
        'project_title',
        'deadline',
        'description',
        'jumlah_poin',
        'file',
        'project_status'
    ];

    public function taskMembers(): hasMany
    {
        return $this->hasMany(TaskMember::class, 'task_member_id');
    }

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
}
