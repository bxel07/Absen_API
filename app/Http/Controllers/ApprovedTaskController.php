<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\ApprovedTask;

class ApprovedTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user()->id;
        $tasks = ApprovedTask::where('user_id', $user)->get();
        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);

    }

    public function show($id)
    {
        $task = ApprovedTask::find($id);
        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $task = ApprovedTask::find($id);
        $task->status = $request->status;
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task Updated',
            'data' => $task
        ]);
    }

}


