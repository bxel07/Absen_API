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
    // Middleware untuk memastikan bahwa pengguna ini telah login.
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createProject(Request $request)
    {
        // Validasi request.
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
        // Jika validasi gagal, kembalikan respon JSON dengan pesan kesalahan.
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation gagal',
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
        $project->jumlah_poin = $request->input('jumlah_poin');
        $project->project_status = $request->input('project_status');
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

    public function editProject(Request $request)
    {
        // Validasi request.
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
        $project = Project::find($request->input('id')); // Cari project berdasarkan ID.
        $project->name = $request->input('name');
        $project->project_title = $request->input('project_title');
        $project->deadline = $request->input('deadline');
        $project->description = $request->input('description');
        $project->jumlah_poin = $request->input('jumlah_poin');
        $project->project_status = $request->input('project_status');
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

    public function deleteProject(Request $request)
    {
        $project = Project::find($request->input('id')); // Cari project berdasarkan ID.
        if (!$project) {
            return response()->json([
                'message' => 'Project tidak ditemukan',
            ], 404);
        }
        // Hapus project.
        $project->delete();
        // Kembalikan respon JSON dengan pesan sukses.
        return response()->json([
            'message' => 'Project telah berhasil dihapus',
        ]);
    }

    public function statusProjects(Request $request)
    {
        // Mendapatkan user yang sedang login.
        $user = Auth::user();
        // Mendapatkan semua project yang dimiliki oleh user tersebut.
        $projects = Project::where('user_id', $user->id);

        // Jika ada query parameter `status`, filter project berdasarkan status tersebut.
        if ($request->has('status')) {
            $projects = $projects->where('project_status', $request->input('status'));
        }
        // Mendapatkan semua project.
        $projects = $projects->get();
        // Kembalikan respon JSON dengan daftar project.
        return response()->json(['projects' => $projects]);
    }

    public function allProjects()
    {
        // Mendapatkan semua project.
        $projects = Project::get();
        // Kembalikan respon JSON dengan daftar project.
        return response()->json(['projects' => $projects]);
    }

    public function detailProject($id)
    {
        // Cari project berdasarkan ID.
        $project = Project::find($id);
        // Jika project tidak ditemukan, kembalikan respon JSON dengan pesan kesalahan
        if (!$project) {
            return response()->json(['message' => 'Project Tidak Ditemukan'], 404);
        }
        // Mendapatkan ID member yang telah diinvite ke project tersebut.
        $taskMemberIds = TaskMember::where('id', $project->task_member_id)->get('user_id')->toArray();
        // Mendapatkan nama lengkap member tersebut.
        $memberFullnames = User::whereIn('id', $taskMemberIds)->get('fullname')->toArray();
        // Buat array berisi detail project.
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
        // Kembalikan respon JSON dengan detail project.
        return response()->json([
            'project' => $projectDetails,

        ]);
    }
}
