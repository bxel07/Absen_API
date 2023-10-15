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
}
