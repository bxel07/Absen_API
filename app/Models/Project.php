<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = ['name', 'description', 'user_id', 'status'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function taskMembers()
    {
        return $this->belongsTo(TaskMembers::class, 'task_member_id', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }
}
