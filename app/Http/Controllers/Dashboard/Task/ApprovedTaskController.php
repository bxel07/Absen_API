<?php

namespace App\Http\Controllers\Dashboard\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovedTask;
use Illuminate\Http\JsonResponse;

class ApprovedTaskController extends Controller
{
       /**
     * @OA\Schema(
     *     schema="Approved Tasks",
     *     type="object",
     *     @OA\Property(property="user_id", type="BigInteger", example = 1),
     *     @OA\Property(property="task_id", type="BigInteger", example = 1),
     *     @OA\Property(property="status", type="string", enum={"pending","approved"}, example="completed"),
     *     @OA\Property(property="comment", type="text", example = "Testing Comment"),
     *
     * )
     */

    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retrieve all to-do lists.
     *
     * @OA\Get(
     *     path="/api/task/task-all",
     *     summary="Project Manager Access - Retrieve all to-do lists",
     *     description="Project Manager retrieves all task data",
     *     tags={"Approved Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: The task was successfully retrieved.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="The task was successfully retrieved."),
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

    /**
     * Method: Mendapatkan semua daftar tugas.
     *
     * @return JsonResponse
     */
    public function index()
    {
        ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->get();
        return response()->json([
            'success' => true,
            'message' => 'The task was successfully retrieved',
            'data' =>  ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->get()
        ], 200);
    }


    /**
     * edit the status in the approved task table.
     *
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/task/task-list",
     *     summary="Project Manager Access - edit the status in the approved task table",
     *     description="The user retrieves all task data",
     *     tags={"Approved Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: status changed successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="status changed successfully."),
     *             @OA\Property(property="data", type="object",
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="task_id", type="integer", example=4),
     *              @OA\Property(property="status", type="string", example="approved"),
     *             ),
     *         ),
     *     ),
     * )
     */


    /**
     * Method: Mengedit tugas yang telah disetujui berdasarkan ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit($id): JsonResponse
    {
        $task = ApprovedTask::find($id);
        $task->user_id = auth()->user()->id;
        $task->status = 'approved';
        $task->save();
        return response()->json([
            'success' => true,
            'data' => $task
        ], 200);
    }


  /**
     * Retrieve all tasks that have pending status.
     *
     * @OA\Get(
     *     path="/api/task-pending",
     *     summary="Project Manager Access - Retrieve all tasks that have pending status",
     *     description="Project Manager retrieves all task data Pending",
     *     tags={"Approved Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: The task was successfully retrieved.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="The task was successfully retrieved."),
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


    /**
     * Method: Mendapatkan daftar tugas yang belum disetujui.
     *
     * @return JsonResponse
     */
    public function taskPending(): JsonResponse
    {
        $task = ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->where('approved_tasks.status', 'pending')->get();
        return response()->json([
            'success' => true,
            'message' => 'The task was successfully retrieved',
            'data' => $task
        ], 200);
    }


  /**
     * Retrieve all tasks that have Approved status.
     *
     * @OA\Get(
     *     path="/api/task-approved",
     *     summary="Project Manager Access - Retrieve all tasks that have Approved status",
     *     description="Project Manager retrieves all task data Approved",
     *     tags={"Approved Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: The task was successfully retrieved.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="The task was successfully retrieved."),
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

    /**
     * Method: Mendapatkan daftar tugas yang telah disetujui.
     *
     * @return JsonResponse
     */
    public function taskApproved(): JsonResponse
    {
        $task = ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->where('approved_tasks.status', 'approved')->get();
        return response()->json([
            'success' => true,
            'message' => 'The task was successfully retrieved',
            'data' => $task
        ], 200);
    }
}
