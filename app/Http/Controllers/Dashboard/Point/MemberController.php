<?php

namespace App\Http\Controllers\Dashboard\Point;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Point;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retrieve data points based on logged in users.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/points",
     *     summary="Project Manager & Member Access - Get Data Points",
     *     description="User retrieves data points based on login",
     *     tags={"Points"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: Data points retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="Data points retrieved successfully."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="success", type="boolean"),
     *                     @OA\Property(property="user_id", type="integer", example= 1),
     *                     @OA\Property(property="main_points", type="integer", example= 300),
     *                     @OA\Property(property="reward_points", type="integer", example= 500),
     *           ),
     *         ),
     *       ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: No data points found for the logged-in user.",
     *         @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="success", type="boolean", example = false),
     *             @OA\Property(property="message", type="string", example="No data points found for the logged-in user."),
     *             @OA\Property(property="data", type="string", example= null),
     *       ),
     *     ),
     *   ),
     * )
     */

    public function index(): JsonResponse
    {
        $user = Auth::user()->id;
        $point = Point::where('user_id', $user)
        ->select('user_id','main_points', 'reward_points')
        ->first();

        if (!$point) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'No data points found for the logged-in user.'
            ], 404); // Menggunakan status 404 untuk menunjukkan data tidak ditemukan.
        }

            return response()->json([
                'success' => true,
                'data' => $point,
                'message' => 'Data points retrieved successfully.'
            ], 200);
    }

     /**
     * Claim Reward Points.
     *
     * @return JsonResponse
     * @param Request $request
     * @OA\Post(
     *     path="/api/claim-rewards",
     *     summary="Project Manager & Member Access - Claim Rewards",
     *     description="User Claim Rewards",
     *     tags={"Points"},
    *       @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="reward_point_before_claims", type="integer"),
    *         ),
    *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: Claim successful.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="Claim successful."),
     *             @OA\Property(property="data", type="object",

     *                     @OA\Property(property="main_points", type="integer", example= 500),
     *                     @OA\Property(property="reward_points", type="integer", example= 500),
     *                     @OA\Property(property="reward_point_before_claims", type="integer", example= 0),

     *         ),
     *       ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Not enough points to claim.",
     *         @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="success", type="boolean", example = false),
     *             @OA\Property(property="message", type="string", example="Not enough points to claim."),
     *             @OA\Property(property="data", type="string", example= null),
     *       ),
     *     ),
     *   ),
     * )
     */
    public function claimReward(Request $request): JsonResponse
    {
        $user = Auth::user()->id;

        if ($user) {
            $reward_point_before_claims = $request->reward_point_before_claims;
            $point = Point::where('user_id', $user)->first();

            if ($point) {
                if ($point->reward_point_before_claims >= $reward_point_before_claims) {
                    $point->reward_point_before_claims -= $reward_point_before_claims;
                    $point->flag_reward_points = false;
                    $point->reward_points += $reward_point_before_claims;
                    $point->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Claim successful',
                        'data' => $point
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough points to claim'
                    ], 404);
                }
            }
        }
    }

    /**
     * Transfer reward_points to main_points
     *
     * @return JsonResponse
     * @param Request $request
     * @OA\Post(
     *     path="/api/transfer-points",
     *     summary="Project Manager & Member Access - Transfer Points",
     *     description="User Transfer reward Points to Main Points",
     *     tags={"Points"},
     *  @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="reward_points", type="integer"),
    *         ),
    *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: Transfer successful.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="Claim successful."),
     *             @OA\Property(property="data", type="object",
     *                     @OA\Property(property="main_points", type="integer", example= 500),
     *                     @OA\Property(property="reward_points", type="integer", example= 0),
     *                     @OA\Property(property="reward_point_before_claims", type="integer", example= 0),
     *
     *       ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: Not enough points to Transfer.",
     *         @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="success", type="boolean", example = false),
     *             @OA\Property(property="message", type="string", example="Not enough points to Transfer."),
     *             @OA\Property(property="data", type="string", example= null),
     *       ),
     *     ),
     *   ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: User points not found.",
     *         @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="success", type="boolean", example = false),
     *             @OA\Property(property="message", type="string", example="User points not found."),
     *             @OA\Property(property="data", type="string", example= null),
     *       ),
     *     ),
     *   ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: User not authenticated.",
     *         @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="success", type="boolean", example = false),
     *             @OA\Property(property="message", type="string", example="User not authenticated."),
     *             @OA\Property(property="data", type="string", example= null),
     *       ),
     *     ),
     *   ),
     * )
     */
    public function transferPoint(Request $request): JsonResponse
    {
        $user = Auth::user()->id;

        if ($user) {
            $reward_points = $request->reward_points;

            $point = Point::where('user_id', $user)->first();

            if ($point) {
                if ($point->reward_points >= $reward_points) {
                    // Mengurangkan  poin dari reward_points
                    $point->reward_points -= $reward_points;

                    // Menambahkan  poin ke main_points
                    $point->main_points += $reward_points;

                    // Menyimpan perubahan
                    $point->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Transfer successful',
                        'data' => $point
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough points to transfer'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User points not found'
                ], 404);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
    }
}
