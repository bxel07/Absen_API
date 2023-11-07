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
     * @OA\Schema(
     *     schema="Task",
     *     type="object",
     *     @OA\Property(property="user_id", type="Integer", example = 3),
     *     @OA\Property(property="name", type="string", example = "Task 1"),
     *     @OA\Property(property="project_id", type="integer", example = 1),
     *     @OA\Property(property="deadline", type="date"),
     *     @OA\Property(property="description", type="text", example = "Ini task 1 dari Project 1"),
     *     @OA\Property(property="status", type="string", enum={"to-do","in progress", "completed"}, example="completed"),
     *     @OA\Property(property="comment", type="text", example = "Testing Comment"),
     *
     * )
     */


    /**
     * Get All Task.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/task/task-list",
     *     summary="Project Manager & Member Access - Get All Task",
     *     description="The user retrieves all task data",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: All Task retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="All Task retrieved successfully."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Tugas Pertama pada Project Pertama"),
     *                     @OA\Property(property="project_id", type="integer", example=1),
     *                     @OA\Property(property="deadline", type="string", example="2023-11-27 10:50:10"),
     *                     @OA\Property(property="description", type="text", example="Ini adalah tugas pertama saya dari Project pertama"),
     *                     @OA\Property(property="status", type="string", enum={"to-do", "in progress", "completed"}, example="completed"),
     *                     @OA\Property(property="comment", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="comment", type="text", example="Ini adalah komentar 1"),
     *                         ),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */

    public function getTaskList(): JsonResponse
    {
        $tasks = Task::all();
        foreach ($tasks as $task) {
            $task->comment = json_decode($task->comment);
        }
        return response()->json([
            'success' => true,
            'message' => 'All Task retrieved successfully',
            'tasks' => $tasks], 200);
    }


    /**
     * Get Task by Project.
     *
     * @OA\Get(
     *     path="/api/task/{project_id}/list-task",
     *     summary="Project Manager & Member Access - Get Task by Project",
     *     description="Users take Tasks based on Projects",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: Assignments based on Project have been successfully taken.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="Assignments based on Project have been successfully taken."),
     *             @OA\Property(property="tasks", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer", example= 1),
     *                     @OA\Property(property="name", type="string", example= "Tugas Pertama pada Project Pertama"),
     *                     @OA\Property(property="project_id", type="integer", example= 1),
     *                     @OA\Property(property="deadline", type="string", example = "2023-11-27 10:50:10"),
     *                     @OA\Property(property="description", type="text", example= "Ini adalah tugas pertama saya dari Project pertama"),
     *                     @OA\Property(property="status", type="string", enum={"to-do","in progress", "completed"}, example="completed"),
     *                     @OA\Property(property="comment", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="comment", type="text", example="Ini adalah komentar 1"),
     *                         ),
     *                     ),
     *           ),
     *         ),
     *       ),
     *     ),
     * )
     */

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
        return response()->json([
            'success' => true,
            'message' => 'Assignments based on Project have been successfully taken',
            'tasks' => $tasks], 200);
    }

    /**
     * Get comments for a specific task.
     *
     * @OA\Get(
     *     path="/api/task/{task_id}/list-comment",
     *     summary="Project Manager & Member Access - Get comments for a specific task",
     *     description="Users take comment based on Projects",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: comments based on the task being successfully retrieved.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="comments based on the task being successfully retrieved."),
     *             @OA\Property(property="tasks", type="object",
     *             @OA\Property(property="comment", type="array",
     *                @OA\Items(
     *                     @OA\Property(property="comment", type="text", example="Ini adalah komentar 1"),
     *            ),
     *           ),
     *         ),
     *       ),
     *     ),
     *
     *  @OA\Response(
     *         response=404,
     *         description="Error: task not found.",
     *         @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="success", type="boolean", example = false),
     *             @OA\Property(property="message", type="string", example= "task not found"),
     *       ),
     *     ),
     *   ),
     * )
     */

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
     * Add Task to Project.
     *
     * @OA\Post(
     *     path="/api/task/{project_id}/add-task",
     *     summary="Project Manager & Member Access - Add Task to Project",
     *     description="User Add Task to Project",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: task added successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="task added successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="string", example="Testing input tugas 1 dari project 1"),
     *                 @OA\Property(property="description", type="string", example="ini isi dari Tugas 1"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Error: You do not have permission to add tasks to this project.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You do not have permission to add tasks to this project."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Project not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Project not found."),
     *         ),
     *     ),
     * )
     */


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

        return response()->json([
            'success' => true,
            'message' => 'task added successfully',
            'data' => $task
        ], 201);
    }


    /**
     * Add Comment to Task.
     *
     * @OA\Post(
     *     path="/api/task/{task_id}/add-comment",
     *     summary="Project Manager & Member Access - Add Comment to Task",
     *     description="User Add Comment to Task",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="comment", type="string"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: comment added successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="comment added successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="string", example="Testing input tugas 1 dari project 1"),
     *                 @OA\Property(property="description", type="string", example="ini isi dari Tugas 1"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *   @OA\Property(property="comment", type="array",
     *                @OA\Items(
     *                     @OA\Property(property="comment", type="text", example="Ini adalah komentar 1"),
     *            ),
     *           ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Comment is Required.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Comment is Required."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Task not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Task not found."),
     *         ),
     *     ),
     * )
     */

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
        return response()->json([
            'success' => true,
            'message' => 'comment added successfully',
            'data' => $task,
           ], 201);
    }


    /**
     * Update Task Status.
     *
     * @OA\Put(
     *     path="/api/task/{task_id}/edit-status",
     *     summary="Project Manager & Member Access - Update Task Status",
     *     description="User Update Task Status",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: task status updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="task status updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Testing input tugas 1 dari project 1"),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *                 @OA\Property(property="deadline", type="string", example="2023-11-27 10:50:10"),
     *                 @OA\Property(property="description", type="string", example="ini isi dari Tugas 1"),
     *                 @OA\Property(property="status", type="string", example="to-do"),
     *                 @OA\Property(property="comment", type="array",
     *                @OA\Items(
     *                     @OA\Property(property="comment", type="text", example="Ini adalah komentar 1"),
     *            ),
     *           ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Status is Required.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Status is Required."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Task not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Task not found."),
     *         ),
     *     ),
     * )
     */

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

        return response()->json([
            'success' => true,
            'message' => 'task status updated successfully',
            'data' => $task,
        ], 200);
    }
}
