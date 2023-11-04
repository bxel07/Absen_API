<?php

namespace App\Http\Controllers\Project_Management;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
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

    public function createProject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'project_title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'reward_point' => 'required|integer',
            'status' => 'required|string|in:to-do,in progress, completed',
            'members' => 'required|array',
            // 'file' => 'nullable|mimes:pdf'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Invite member ke project baru.
        $taskMemberIds = $request->input('members');
        foreach ($taskMemberIds as $memberId) {
            $taskMember = new TaskMember();
            $taskMember->user_id = $memberId;
            $taskMember->save();
        }

        // Simpan file project (opsional).
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->storeAs('public/documents/project', $file->hashName());
        }

        // Buat project baru.
        $project = new Project();
        $project->user_id = Auth::id(); // Set user ID proyek ke ID pengguna yang sedang login.
        $project->name = $request->input('name');
        $project->project_title = $request->input('project_title');
        $project->deadline = $request->input('deadline');
        $project->description = $request->input('description');
        $project->reward_point = $request->input('reward_point');
        $project->status = $request->input('status');
        $project->task_member_id = $taskMember->id;
        $project->file = $filePath;
        $project->save();

        $memberFullnames = User::whereIn('id', $taskMemberIds)->get('fullname')->toArray();
        // Kembalikan respon JSON dengan pesan sukses dan data project.
        return response()->json([
            'message' => 'Project Baru Berhasil Dibuat',
            'project' => $project,
            'member_fullnames' => $memberFullnames,
        ]);
    }

    public function editProject(Request $request, $id)
    {
        // Validasi request.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'project_title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'reward_point' => 'required|integer',
            'status' => 'required|string|in:to-do,in progress, completed',
            'members' => 'required|array',
            // 'file' => 'nullable|mimes:pdf'
        ]);
        // Jika validasi gagal, kembalikan respon JSON dengan pesan kesalahan.
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation gagal',
                'errors' => $validator->errors(),
            ], 400);
        }
        // Invite member ke project yang diedit.
        $taskMemberIds = $request->input('members');
        foreach ($taskMemberIds as $memberId) {
            $taskMember = new TaskMember();
            $taskMember->user_id = $memberId;
            $taskMember->save();
        }
        // Simpan file project (opsional).
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->storeAs('public/documents/project', $file->hashName());
        }

        // Perbarui data project.
        $project = Project::find($id); // Cari project berdasarkan ID.

        $project->name = $request->input('name');
        $project->project_title = $request->input('project_title');

        $project->deadline = $request->input('deadline');
        $project->description = $request->input('description');
        $project->reward_point = $request->input('reward_point');
        $project->status = $request->input('status');
        $project->task_member_id = $taskMember->id;
        $project->file = $filePath;
        $project->save();

        $memberFullnames = User::whereIn('id', $taskMemberIds)->get('fullname')->toArray();
        // Kembalikan respon JSON dengan pesan sukses dan data project.
        return response()->json([
            'message' => 'Project Berhasil Diedit',
            'project' => $project,
            'member_fullnames' => $memberFullnames,
        ]);
    }

    public function deleteProject(Request $request, $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json([
                'message' => 'Project tidak ditemukan',
            ], 404);
        }

        Storage::delete($project->file);

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
            'status' => $project->status,
            'task_member_fullname' => $memberFullnames,
        ];

        return response()->json([
            'project' => $projectDetails,

        ]);
    }
}
