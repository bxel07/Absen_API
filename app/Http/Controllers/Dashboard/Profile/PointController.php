<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PointController extends Controller
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
        $point = Point::find($user); // Find the point associated with the current user

        return response()->json([
            'success' => true,
            'data' => $point
        ],200);

    }


    public function getData()
    {
        point::all();
        return response()->json([
            'success'=> true,
            'data'=> Point::all()
            ],200);
    }

    public function addMainPoint(Request $request)
    {
        $user_id = $request->user_id;
        $main_points = $request->main_points;

        $point = Point::where('user_id', $user_id)->first();

        if($point){
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

    public function addRewardPoint(Request $request)
    {
        $user_id = $request->user_id;
        $reward_points = $request->reward_points;

        $point = Point::where('user_id', $user_id)->first();
        if($point){
            $point->reward_points += $reward_points;
            $point->save();
        } else {
            $userData = [
                'user_id' => $user_id,
                'reward_points' => $reward_points,
            ];
            $point = Point::create($userData);
        }
        return response()->json([
            'success' => true,
            'data' => $point
        ], 200);
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
