<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    protected $fillable = ['name', 'description', 'user_id', 'project_id', 'project_title'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
