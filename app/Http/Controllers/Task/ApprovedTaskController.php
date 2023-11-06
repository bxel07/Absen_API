<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovedTask;

class ApprovedTaskController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        ApprovedTask::join('tasks', 'approved_tasks.task_id','=','tasks.id')->get();
       return response()->json([
        'success' => true,
        'data' =>  ApprovedTask::join('tasks', 'approved_tasks.task_id','=','tasks.id')->get()
       ], 200);
    }

    public function edit($id)
    {
        $task = ApprovedTask::find($id);
        $task->user_id = auth()->user()->id;
        $task->status = 'approved';
        $task->save();
        return response()->json([
            'success'=> true,
            'data' => $task
            ],200);
    }

    public function taskPending()
    {
        $task = ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->where('approved_tasks.status', 'pending')->get();
        return response()->json([
            'success'=> true,
            'data' => $task
            ], 200);
    }

    public function taskApproved()
    {
        $task = ApprovedTask::join('tasks', 'approved_tasks.task_id', '=', 'tasks.id')->where('approved_tasks.status', 'approved')->get();
        return response()->json([
            'success'=> true,
            'data'=> $task
        ], 200);
    }
}
