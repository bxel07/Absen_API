<?php

namespace App\Http\Controllers\Dashboard\ProjectManagement;

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

    /**
     * Method: Membuat project baru.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createProject(Request $request): JsonResponse
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

        // Upload the file
        $url = $this->uploadFile($request);

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
        $project->file = $url;
        $project->save();
        // Dapatkan nama lengkap member project.
        $memberFullnames = User::whereIn('id', $taskMemberIds)->get('fullname')->toArray();
        // Kembalikan respon JSON dengan pesan sukses dan data project.
        return response()->json([
            'message' => 'Project Baru Berhasil Dibuat',
            'project' => $project,
            'member_fullnames' => $memberFullnames,
        ]);
    }

    /**
     * Method: Mengedit project yang ada.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function editProject(Request $request, $id): JsonResponse
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

        $project = Project::find($id); // Cari project berdasarkan ID.

        // Invite member ke project yang diedit.
        $taskMemberIds = $request->input('members');
        foreach ($taskMemberIds as $memberId) {
            $taskMember = new TaskMember();
            $taskMember->user_id = $memberId;
            $taskMember->save();
        }
        //upload file
        $request->hasFile('file');  // Hapus file sebelumnya jika ada.
        Storage::delete('public/documents/' . $project->file);
        // Upload file baru jika ada.
        $url = $this->uploadFile($request);

        // Perbarui data project.
        $project->name = $request->input('name');
        $project->project_title = $request->input('project_title');
        $project->deadline = $request->input('deadline');
        $project->description = $request->input('description');
        $project->reward_point = $request->input('reward_point');
        $project->status = $request->input('status');
        $project->task_member_id = $taskMember->id;
        $project->file = $url;
        $project->save();
        // Dapatkan nama lengkap member project.
        $memberFullnames = User::whereIn('id', $taskMemberIds)->get('fullname')->toArray();
        // Kembalikan respon JSON dengan pesan sukses dan data project.
        return response()->json([
            'message' => 'Project Berhasil Diedit',
            'project' => $project,
            'member_fullnames' => $memberFullnames,
        ]);
    }

    /**
     * Method: Menghapus project berdasarkan ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteProject($id): JsonResponse
    {
        // Cari project berdasarkan ID.
        $project = Project::where('id', $id)->first();
        // Hapus file project jika ada.
        if (!is_null($project->file)) {
            $data = basename($project->file);
            Storage::delete('public/documents/' . $data);
        }
        // Hapus project.
        $delProject = Project::find($id)->delete();
        // Kembalikan respon JSON dengan pesan sukses.
        if ($delProject) {
            return response()->json([
                'success' => true,
                'message' => 'Data ID project ' . $id . ' berhasil dihapus!',
                'data' => null,
            ], 200);
        }
    }

    /**
     * Method: Mendapatkan project berdasarkan status.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function statusProjects(Request $request): JsonResponse
    {
        // Dapatkan user saat ini.
        $user = Auth::user();
        // Query project berdasarkan user ID.
        $projects = Project::where('user_id', $user->id);
        // Jika ada parameter status, filter project berdasarkan status.
        if ($request->has('status')) {
            $projects = $projects->where('project_status', $request->input('status'));
        }
        // Dapatkan project.
        $projects = $projects->get();
        // Kembalikan respon JSON dengan data project.
        return response()->json(['projects' => $projects]);
    }

    /**
     * Method: Mendapatkan semua project.
     *
     * @return JsonResponse
     */
    public function allProjects(): JsonResponse
    {
        // Dapatkan project.
        $projects = Project::get();
        // Kembalikan respon JSON dengan data project.
        return response()->json(['projects' => $projects]);
    }

    /**
     * Method: Mendapatkan detail project berdasarkan ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function detailProject($id): JsonResponse
    {
        // Cari project berdasarkan ID.
        $project = Project::find($id);
        // Jika project tidak ditemukan, kembalikan respon JSON dengan pesan kesalahan.
        if (!$project) {
            return response()->json(['message' => 'Project Tidak Ditemukan'], 404);
        }
        // Dapatkan ID member project.
        $taskMemberIds = TaskMember::where('id', $project->task_member_id)->get('user_id')->toArray();
        // Dapatkan nama lengkap member project.
        $memberFullnames = User::whereIn('id', $taskMemberIds)->get('fullname')->toArray();

        // Buat array detail project.
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

        // Kembalikan respon JSON dengan data detail project.
        return response()->json([
            'project' => $projectDetails,

        ]);
    }

    /**
     * Method: Mengunggah file.
     *
     * @param Request $request
     * @return string
     */
    public function uploadFile(Request $request): string
    {
        //upload file
        $file = $request->file('file');
        $file->storeAs('public/documents', $file->hashName());

        $getAllRequest = $request->all();
        $getAllRequest['file'] = $file->hashName();
        $url = Storage::url('public/documents/' . $getAllRequest['file']);

        return $url;
    }
}
