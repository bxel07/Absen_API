<?php

namespace App\Http\Controllers\Dashboard\Point;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Point;
use Illuminate\Http\JsonResponse;

class ProjectManagerController extends Controller
{
    /**
     * Retrieve the point associated with the current user.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/point",
     *     summary="Retrieve the point associated with the current user",
     *     description="Retrieve the point associated with the current user based on the user's ID.",
     *     tags={"Profile"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data"),
     *         ),
     *     ),
     * )
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Method: Mengambil poin yang terkait dengan pengguna saat ini.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        point::all();
        return response()->json([
            'success' => true,
            'data' => Point::all()
        ], 200);
    }

    /**
     * Method: Menambahkan poin utama (main_points) ke akun pengguna.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addMainPoint(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $main_points = $request->main_points;

        $point = Point::where('user_id', $user_id)->first();

        if ($point) {
            $point->main_points += $main_points;
            $point->save();
        } else {
            $userData = [
                'user_id' => $user_id,
                'main_points' => $main_points,
            ];
            $point = Point::create($userData);
        }
        return response()->json([
            'success' => true,
            'data' => $point
        ], 200);
    }

    /**
     * Method: Menambahkan poin hadiah sebelum klaim (reward_point_before_claims) ke akun pengguna.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addRewardPointBeforeClaims(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $add_points = $request->reward_point_before_claims;

        $point = Point::where('user_id', $user_id)->first();
        if ($point) {
            $point->reward_point_before_claims += $add_points;
            $point->flag_reward_points = true;
            $point->save();
        } else {
            $userData = [
                'user_id' => $user_id,
                'reward_point_before_claims' => $add_points
            ];
            $point = Point::create($userData);
        }
        return response()->json([
            'success' => true,
            'data' => $point
        ], 200);
    }
}
