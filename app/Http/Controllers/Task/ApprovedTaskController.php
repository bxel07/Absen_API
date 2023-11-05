<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovedTask;
use Illuminate\Http\JsonResponse;

class ApprovedTaskController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Mendapatkan semua daftar tugas yang telah disetujui
    public function index()
    {
        ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->get();
        return response()->json([
            'success' => true,
            'data' =>  ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->get()
        ], 200);
    }

    // Menytujui tugas tertentu
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

    // Mendapatkan daftar tugas yang belum disetujui
    public function taskPending(): JsonResponse
    {
        $task = ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->where('approved_tasks.status', 'pending')->get();
        return response()->json([
            'success' => true,
            'data' => $task
        ], 200);
    }

    // Mendapatkan daftar tugas yang telah disetujui
    public function taskApproved(): JsonResponse
    {
        $task = ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->where('approved_tasks.status', 'approved')->get();
        return response()->json([
            'success' => true,
            'data' => $task
        ], 200);
    }
}
