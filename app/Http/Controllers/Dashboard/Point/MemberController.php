<?php

namespace App\Http\Controllers\Dashboard\Point;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Point;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
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

    public function index(): JsonResponse
    {
        $user = Auth::user()->id;
        $point = Point::where('user_id', $user)->first();

        return response()->json([
            'success' => true,
            'data' => $point
        ],200);

    }

    public function claimReward(Request $request)
    {
        $user = Auth::user()->id;

        if($user){
            $reward_point_before_claims = $request->reward_point_before_claims;
            $point = Point::where('user_id', $user)->first();

            if  ($point) {
                if($point->reward_point_before_claims >= $reward_point_before_claims){
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
                    ], 400);
                }
            }
        }
    }

    public function transferPoint(Request $request)
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
