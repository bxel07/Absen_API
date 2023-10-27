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
        ApprovedTask::all();
       return response()->json([
        'success' => true,
        'data' => ApprovedTask::all()
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
}
