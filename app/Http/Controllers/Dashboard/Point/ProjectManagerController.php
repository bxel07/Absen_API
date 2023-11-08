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
     * @OA\Schema(
     *     schema="Point",
     *     type="object",
     *     @OA\Property(property="user_id", type="bigInteger", example = 3),
     *     @OA\Property(property="main_points", type="bigInteger", example = 200),
     *     @OA\Property(property="reward_points", type="bigInteger", example = 300),
     *     @OA\Property(property="flag_reward_points", type="boolean", example = 1),
     *     @OA\Property(property="reward_point_before_claims", type="bigInteger", example = 100),
     * )
     */

    /**
     * Retrieve the points associated with all current users.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/data-points",
     *     summary="Project Manager Access - List All Points User",
     *     description="Project Manager takes data points from all members.",
     *     tags={"Points"},
     *     security={{ "bearerAuth": {} }},
     *    @OA\Response(
     *         response=200,
     *         description="Success: User points found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="message", type="string", example="User points found."),

     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer", example= 1),
     *                     @OA\Property(property="main_points", type="integer", example= 300),
     *                     @OA\Property(property="reward_points", type="integer", example= 500),
     *           ),
     *         ),
     *       ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Didn't find any user data points.",
     *         @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="success", type="boolean", example = false),
     *             @OA\Property(property="message", type="string", example="Didn't find any user data points."),
     *             @OA\Property(property="data", type="string", example= null),
     *       ),
     *     ),
     *   ),
     * )
     */

    public function getData(): JsonResponse
    {
        $point = point::all();

        if(!$point) {
        return response()->json([
            'succcess' => false,
            'data' => null,
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
     * Sending main points to users
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/add-main-points",
     *     summary="Project Manager Access - Send Main Points",
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
     *
     *     @OA\Response(
     *         response=200,
     *         description="Success: Point sent.",
     *         @OA\JsonContent(
     *             type="object",
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="message", type="string", example="Point Sent"),
     *             @OA\Property(property="data", type="object",
     *                     @OA\Property(property="user_id", type="integer", example= 3),
     *                     @OA\Property(property="main_points", type="integer", example= 500),
     *                     @OA\Property(property="reward_points", type="integer", example= 0),
     *         ),
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
            'message' => 'Point Sent'
        ], 200);
    }


    /**
     * Method: Add reward points before claiming (reward_point_before_claims) to the user's account
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/add-rewards",
     *     summary="Project Manager Access - Send Reward Points",
     *     description="Send reward points from the project manager to the project manager or members.",
     *     tags={"Points"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer",  format="int64", description="Id users."),
     *             @OA\Property(property="reward_point_before_claims", type="integer", format="int64", description="Points"),
     *         ),
     *     ),
     *
     *      @OA\Response(
     *         response=200,
     *         description="Success: Reward points added successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="message", type="string", example="Reward points added successfully"),
     *             @OA\Property(property="data", type="object",
     *                   @OA\Property(property="user_id", type="integer", example= 3),
     *                     @OA\Property(property="main_points", type="integer", example= 0),
     *                     @OA\Property(property="reward_points", type="integer", example= 0),
     *                     @OA\Property(property="reward_point_before_claims", type="integer", example= 500),
     *         ),
     *         ),
     *     ),
     * )
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
            'data' => $point,
            'message'=> 'Reward points added successfully.'
        ], 200);
    }
}
