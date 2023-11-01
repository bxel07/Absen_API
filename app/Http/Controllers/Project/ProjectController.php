<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\TaskMember;

class ProjectController extends Controller
{
    public function getProjectsAndTasks($user_id)
    {
        //menampilkan data project dan task setiap user tersebut.
        $taskMembers = TaskMember::where('user_id', $user_id)
            ->with('projects.tasks')
            ->get();
        if ($taskMembers->isEmpty()) {
            return response()->json(['message' => 'User belum memiliki project'], 404);
        }

        // Menampilkan daftar Project dan Task yang terkait dengan user tersebut.
        return response()->json(['projects_and_tasks' => $taskMembers], 200);
    }
}
