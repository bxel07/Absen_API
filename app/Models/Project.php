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
}
