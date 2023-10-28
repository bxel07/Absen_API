<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_Member extends Model
{
    public function projects()
    {
        return $this->hasMany(Project::class, 'task_member_id', 'user_id');
    }
}
