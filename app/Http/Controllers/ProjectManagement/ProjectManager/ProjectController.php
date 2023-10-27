<?php

namespace App\Http\Controllers\ProjectManagement\ProjectManager;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskMember;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'project_title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'jumlah_poin' => 'required|integer',
            'project_status' => 'required|string|in:rencana,berjalan,selesai',
            'members' => 'required|array',
            // 'file' => 'nullable|mimes:pdf'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $project = new Project();
        $project->user_id = Auth::id();
        $project->name = $request->input('name');
        $project->project_title = $request->input('project_title');
        $project->deadline = $request->input('deadline');
        $project->description = $request->input('description');
        $project->jumlah_poin = $request->input('jumlah_poin');
        $project->project_status = $request->input('project_status');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->storeAs('public', $file->hashName());
            $project->file = $filePath;
        }

        $taskMemberIds = $request->input('members');
        foreach ($taskMemberIds as $memberId) {
            $taskMember = new TaskMember();
            $taskMember->user_id = $memberId;
            $taskMember->save();
        }

        $project->task_member_id = $taskMember->id;
        $project->save();
        $memberFullnames = User::whereIn('id', $request->input('members'))->get('fullname')->toArray();
        return response()->json([
            'message' => 'Project Baru Berhasil Dibuat',
            'project' => $project,
            'member_fullnames' => $memberFullnames,
        ]);
    }

    public function editProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'project_title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'jumlah_poin' => 'required|integer',
            'project_status' => 'required|string|in:rencana,berjalan,selesai',
            'members' => 'required|array',
            // 'file' => 'nullable|mimes:pdf'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $project = Project::find($request->input('id'));

        $project->name = $request->input('name');
        $project->project_title = $request->input('project_title');
        $project->deadline = $request->input('deadline');
        $project->description = $request->input('description');
        $project->jumlah_poin = $request->input('jumlah_poin');
        $project->project_status = $request->input('project_status');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->storeAs('public', $file->hashName());
            $project->file = $filePath;
        }

        $taskMemberIds = $request->input('members');
        foreach ($taskMemberIds as $memberId) {
            $taskMember = new TaskMember();
            $taskMember->user_id = $memberId;
            $taskMember->save();
        }

        $project->task_member_id = $taskMember->id;
        $project->save();
        $memberFullnames = User::whereIn('id', $request->input('members'))->get('fullname')->toArray();
        return response()->json([
            'message' => 'Project Berhasil Diedit',
            'project' => $project,
            'member_fullnames' => $memberFullnames,
        ]);
    }

    public function deleteProject(Request $request)
    {
        $project = Project::find($request->input('id'));
        if (!$project) {
            return response()->json([
                'message' => 'Project tidak ditemukan',
            ], 404);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project telah berhasil dihapus',
        ]);
    }

    public function statusProjects(Request $request)
    {
        $user = Auth::user();
        $projects = Project::where('user_id', $user->id);

        if ($request->has('status')) {
            $projects = $projects->where('project_status', $request->input('status'));
        }

        $projects = $projects->get();
        return response()->json(['projects' => $projects]);
    }

    public function allProjects()
    {
        $projects = Project::get();

        return response()->json(['projects' => $projects]);
    }

    public function detailProject($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project Tidak Ditemukan'], 404);
        }
        $taskMemberIds = TaskMember::where('id', $project->task_member_id)->get('user_id')->toArray();
        $memberFullnames = User::whereIn('id', $taskMemberIds)->get('fullname')->toArray();
        $projectDetails = [
            'id' => $project->id,
            'task_member_id' => $project->task_member_id,
            'user_id' => $project->user_id,
            'name' => $project->name,
            'project_title' => $project->project_title,
            'deadline' => $project->deadline,
            'description' => $project->description,
            'jumlah_poin' => $project->jumlah_poin,
            'file' => $project->file,
            'project_status' => $project->project_status,
            'task_member_fullname' => $memberFullnames,
        ];

        return response()->json([
            'project' => $projectDetails,

        ]);
    }
}
