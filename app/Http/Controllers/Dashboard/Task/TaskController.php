<?php

namespace App\Http\Controllers\Dashboard\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\ApprovedTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Method: Mendapatkan semua daftar tugas.
     *
     * @return JsonResponse
     */
    public function getTaskList(): JsonResponse
    {
        $tasks = Task::all();
        foreach ($tasks as $task) {
            $task->comment = json_decode($task->comment);
        }
        return response()->json(['tasks' => $tasks], 200);
    }

    /**
     * Method: Mendapatkan daftar tugas berdasarkan project.
     *
     * @param int $project_id
     * @return JsonResponse
     */
    public function getTasksByProject($project_id): JsonResponse
    {
        $tasks = Task::where('project_id', $project_id)->get();
        foreach ($tasks as $task) {
            $task->comment = json_decode($task->comment);
        }
        return response()->json(['tasks' => $tasks], 200);
    }

    /**
     * Method: Mendapatkan komentar untuk tugas tertentu.
     *
     * @param int $task_id
     * @return JsonResponse
     */
    public function getCommentsForTask($task_id): JsonResponse
    {
        $task = Task::find($task_id);
        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }
        $comment = json_decode($task->comment);
        return response()->json(['comment' => $comment], 200);
    }

    /**
     * Method: Menambahkan tugas ke project.
     *
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function addTaskToProject(Request $request, $project_id): JsonResponse
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
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
        if ($project->task_member_id != $user->id) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk menambahkan tugas ke proyek ini.'], 403);
        }

        // Buat tugas baru
        $task = new Task();
        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->user_id = $user->id;
        $task->project_id = $project_id;
        $task->save();

        $approval = new ApprovedTask();
        $approval->task_id = $task->id;
        $approval->save();

        return response()->json(['message' => 'Tugas berhasil ditambahkan'], 201);
    }

    /**
     * Method: Menambahkan komentar ke tugas.
     *
     * @param Request $request
     * @param int $task_id
     * @return JsonResponse
     */
    public function addCommentToTask(Request $request, $task_id): JsonResponse
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
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

        // Mendapatkan komentar yang ada dalam tugas
        $existingComments = json_decode($task->comment, true) ?? [];
        // Mendapatkan komentar baru dari permintaan
        $newComment = $request->input('comment');
        // Menambahkan komentar baru ke dalam array komentar yang sudah ada
        $existingComments[] = $newComment;
        // Simpan komentar-komentar dalam bentuk array
        $task->comment = json_encode($existingComments);
        $task->save();
        return response()->json(['message' => 'Komentar berhasil ditambahkan'], 201);
    }
    
    /**
    * Mengubah status tugas berdasarkan ID tugas.
    *
    * @param Request $request
    * @param int $task_id
    * @return JsonResponse
    */
    public function updateTaskStatus(Request $request, $task_id): JsonResponse
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:to-do,in progress,completed', // Sesuaikan dengan status yang diperlukan.
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Temukan tugas yang sesuai dengan $task_id
        $task = Task::find($task_id);

        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        // Perbarui status tugas
        $task->status = $request->input('status');
        $task->save();

        return response()->json(['message' => 'Status tugas berhasil diperbarui'], 200);
    }
}
