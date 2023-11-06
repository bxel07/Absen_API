<?php

namespace App\Http\Controllers\Dashboard\Point;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Point;
use Illuminate\Http\JsonResponse;

class ProjectManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

      /**
     * Retrieve the points associated with all current users.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/data-points",
     *     summary="Retrieve the points associated with all current users",
     *     description="Project Manager takes data points from all members.",
     *     tags={"Points"},
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

       /**
     * Send email verification with OTP for password reset.
     *
     * @OA\Post(
     *     path="/api/add-main-points",
     *     summary="Sending main points to users",
     *     description="Send key points from the project manager to the project manager or members.",
     *     tags={"Points"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer",  format="int64", description="Id users."),
     *             @OA\Property(property="main_points", type="integer", format="int64", description="Points"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Point sent.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     * )
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
            'data' => $point,
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
