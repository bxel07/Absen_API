<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskMember;

class ProjectController extends Controller
{
    // Middleware untuk memastikan bahwa pengguna ini telah login.
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function getProjectsAndTasks($user_id)
    {
        // Mendapatkan user yang sedang login.
        $user = Auth::user();

        // Memeriksa apakah user ID yang diberikan adalah anggota dari proyek yang dimaksud.
        $isMember = TaskMember::where('user_id', $user->id)
            ->whereHas('projects', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'Anda tidak memiliki akses pada halaman ini'], 404);
        }

        // Mendapatkan daftar project dan tugas yang terkait dengan user tersebut.
        $taskMembers = TaskMember::where('user_id', $user->id)
            ->whereHas('projects', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->with('projects.tasks')
            ->get();

        return response()->json(['projects_and_tasks' => $taskMembers], 200);
    }
}