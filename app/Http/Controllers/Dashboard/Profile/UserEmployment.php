<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserEmployment extends Controller
{
    /**
     * Retrieve employment details for the authenticated user.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/user-employment",
     *     summary="Retrieve employment details for the authenticated user",
     *     description="Retrieve employment details for the authenticated user based on the user's ID.",
     *     tags={"Profile"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=""),
     *         ),
     *     ),
     * )
     */
    public function index(): JsonResponse
    {

        // User-Id
        $user_id = Auth::user()->id;

        // Fetch data related to the employments table
        $data = DB::table('employments')
            ->join('departments', 'employments.department_id', '=', 'departments.id')
            ->join('job_positions', 'departments.job_position_id', '=', 'job_positions.id')
            ->join('job_levels', 'departments.job_level_id', '=', 'job_levels.id')
            ->select('employments.*', 'departments.name as department_name', 'job_positions.name as job_position_name', 'job_levels.name as job_level_name')
            ->where('employments.user_id', '=', $user_id)
            ->get();
        return response()->json($data, 200);
    }
}
