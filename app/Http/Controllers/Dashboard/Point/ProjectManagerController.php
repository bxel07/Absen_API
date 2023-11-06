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
     *    @OA\Response(
     *         response=200,
     *         description="Success: Data points retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: No data points found for the logged-in user.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     * )
     */

    public function getData(): JsonResponse
    {
        $point = point::all();

        if(!$point) {
        return response()->json([
            'message' => "Didn't find any user data points"
        ],404);
        }

        return response()->json([
            'success' => true,
            'data' => Point::all(),
            'message' => 'User points found',
        ], 200);
    }


    /**
     * Sending main points to users.
     *
     * @OA\Post(
     *     path="/api/add-main-points",
     *     summary="Sending main points to users",
     *     description="Send main points from the project manager to the project manager or members.",
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

     /**
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
            'data' => $point,
            'message' => 'Point Sent'
        ], 200);
    }


    /**
     * Method: Add reward points before claiming (reward_point_before_claims) to the user's account
     *
     * @OA\Post(
     *     path="/api/add-rewards",
     *     summary="Sending reward points to users",
     *     description="Send reward points from the project manager to the project manager or members.",
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
