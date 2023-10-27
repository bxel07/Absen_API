<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class TaskController extends Controller
{
    public function getTaskList()
    {
        $tasks = Task::all(); // Mengambil semua tugas

        return response()->json(['tasks' => $tasks], 200);
    }

    public function getTasksByProject($project_id)
    {
        $tasks = Task::where('project_id', $project_id)->get();

        // Memisahkan komentar dari tugas dan mengembalikannya dalam tampilan yang lebih baik
        $formattedTasks = $tasks->map(function ($task) {
            $comment = explode("\n", $task->comment);
            return [
                'id' => $task->id,
                'user_id' => $task->user_id,
                'name' => $task->name,
                'project_id' => $task->project_id,
                'project_title' => $task->project_title,
                'description' => $task->description,
                'comment' => $comment,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ];
        });

        return response()->json(['tasks' => $formattedTasks], 200);
    }


    public function getCommentsForTask($task_id)
    {
        $task = Task::find($task_id);
        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }
        $comment = explode("\n", $task->comment);
        return response()->json(['comment' => $comment], 200);
    }

    // public function addTaskToProject(Request $request, $project_id)
    // {
    //     // Validasi data yang diterima dari permintaan
    //     $validator = \Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'description' => 'required|string',
    //     ]);

    //     // Temukan proyek yang sesuai dengan $project_id
    //     $project = Project::find($project_id);

    //     if (!$project) {
    //         return response()->json(['message' => 'Proyek tidak ditemukan'], 404);
    //     }

    //     // Pastikan pengguna yang melakukan permintaan adalah admin proyek
    //     if ($project->user_id !== auth()->user()->id) {
    //         return response()->json(['message' => 'Anda tidak memiliki izin untuk menambahkan tugas ke proyek ini.'], 403);
    //     }

    //     // Buat tugas baru
    //     $task = new Task();
    //     $task->name = $request->input('name');
    //     $task->description = $request->input('description');
    //     $task->user_id = auth()->user()->id;
    //     $task->project_id = $project_id;
    //     $task->project_title = $project->project_title;
    //     $task->save();

    //     return response()->json(['message' => 'Tugas berhasil ditambahkan'], 201);
    // }
    public function addTaskToProject(Request $request, $project_id)
    {
        // Validasi data yang diterima dari permintaan
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        // Temukan proyek yang sesuai dengan $project_id
        $project = Project::find($project_id);

        if (!$project) {
            return response()->json(['message' => 'Proyek tidak ditemukan'], 404);
        }

        // Pastikan pengguna yang melakukan permintaan adalah anggota proyek yang sah
        $user = auth()->user();
        $isMember = $project->task_member_id == $user->id;

        if (!$isMember) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk menambahkan tugas ke proyek ini.'], 403);
        }

        // Buat tugas baru
        $task = new Task();
        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->user_id = $user->id;
        $task->project_id = $project_id;
        $task->project_title = $project->project_title;
        $task->save();

        return response()->json(['message' => 'Tugas berhasil ditambahkan'], 201);
    }


    public function addCommentToTask(Request $request, $task_id)
    {
        // Validasi data yang diterima dari permintaan
        $validator = \Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Temukan tugas yang sesuai dengan $task_id
        $task = Task::find($task_id);

        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        // Tambahkan komentar ke tugas
        $comment = $request->input('comment');
        $task->comment .= $comment . "\n";
        $task->save();

        return response()->json(['message' => 'Komentar berhasil ditambahkan'], 201);
    }
}
