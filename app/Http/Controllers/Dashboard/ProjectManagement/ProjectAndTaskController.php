<?php

namespace App\Http\Controllers\Dashboard\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\TaskMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class ProjectAndTaskController extends Controller
{
    // Middleware untuk memastikan bahwa pengguna ini telah login.
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Retrieve the points associated with all current users.
     *
     * @param $user_id
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/get-projects-and-tasks/{user_id}",
     *     summary="Get a list of projects and user tasks by ID",
     *     tags={"Projects Mangements and Task"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: Data Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="projects_and_tasks",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer", example=1001),
     *                     @OA\Property(property="main_points", type="integer", example=120),
     *                     @OA\Property(property="reward_points", type="integer", example=50),
     *                     @OA\Property(
     *                         property="projects",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="task_member_id", type="integer", example=1),
     *                         @OA\Property(property="user_id", type="integer", example=1001),
     *                         @OA\Property(property="name", type="string", example="John"),
     *                         @OA\Property(property="project_title", type="string", example="Project A"),
     *                         @OA\Property(property="deadline", type="string", example="2023-11-10 15:00:00"),
     *                         @OA\Property(property="description", type="string", example="Project A details"),
     *                         @OA\Property(property="reward_point", type="integer", example=50),
     *                         @OA\Property(property="file", type="string", example=null),
     *                         @OA\Property(property="status", type="string", example="in progress"),
     *                         @OA\Property(property="created_at", type="string", example="2023-11-08 10:05:00"),
     *                         @OA\Property(property="updated_at", type="string", example="2023-11-08 10:35:00"),
     *                         @OA\Property(
     *                             property="tasks",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="user_id", type="integer", example=1001),
     *                                 @OA\Property(property="name", type="string", example="Task1"),
     *                                 @OA\Property(property="project_id", type="integer", example=1),
     *                                 @OA\Property(property="deadline", type="string", example="2023-11-09 10:00:00"),
     *                                 @OA\Property(property="description", type="string", example="Task1 details"),
     *                                 @OA\Property(property="status", type="string", example="in progress"),
     *                                 @OA\Property(property="comment", type="string", example="Good job"),
     *                                 @OA\Property(property="created_at", type="string", example="2023-11-08 10:10:00"),
     *                                 @OA\Property(property="updated_at", type="string", example="2023-11-08 10:40:00")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Cannot access this page",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Anda tidak memiliki akses pada halaman ini")
     *         )
     *     )
     * )
     */
    public function getProjectsAndTasks($user_id): JsonResponse
    {
        // Mendapatkan user yang sedang login.
        $user = Auth::user();

        // Memeriksa apakah user ID yang diberikan adalah anggota dari proyek yang dimaksud.
        $isMember = TaskMember::where('user_id', $user->id)
            ->whereHas('projects', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'Anda tidak memiliki akses pada halaman ini'], 404);
        }

        // Mendapatkan daftar project dan tugas yang terkait dengan user tersebut.
        $taskMembers = TaskMember::where('user_id', $user->id)
            ->whereHas('projects', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->with('projects.tasks')
            ->get();

        return response()->json(['projects_and_tasks' => $taskMembers], 200);
    }
}
