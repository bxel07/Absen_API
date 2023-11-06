<?php

namespace App\Http\Controllers\Dashboard\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Shows all notifications.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/all-notifications",
     *     summary="Shows all notifications",
     *     description="Project Manager can view/retrieve clock in and clock out request data from users (members).",
     *     tags={"Notifications"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: Notification data list was successfully retrieved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="Notification Data List"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Clock In"),
     *                     @OA\Property(property="message", type="string", example="User makes a clock in request."),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="read_status_for_admin", type="integer", example=0),
     *                     @OA\Property(property="read_status_for_user", type="integer", example=0),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Notification Data List Not Found!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Notification Data List Not Found"),
     *             @OA\Property(property="data", type="string", example=null),
     *         ),
     *     ),
     * )
     */
    public function allNotifications(): JsonResponse
    {
        $allNotifications = Notification::latest()->get();
        if ($allNotifications) {
            return response()->json([
                'success' => true,
                'message' => 'Notification Data List',
                'data'    => $allNotifications
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Notification Data List Not Found!',
                'data'    => null
            ], 404);
        }
    }

    /**
     * Display notification history based on user ID.
     *
     * @param int $user_id
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/user-notifications/{user_id}",
     *     summary="Display notification history based on user ID",
     *     description="Users can view their own notification history.",
     *     tags={"Notifications"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: User notification data history was successfully retrieved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="List of Notification Data from User ID (1)"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="Clock In"),
     *                     @OA\Property(property="message", type="string", example="User makes a clock out request."),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="read_status_for_admin", type="integer", example=0),
     *                     @OA\Property(property="read_status_for_user", type="integer", example=0),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: User Notification Data History Not Found!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User Notification Data History Not Found"),
     *             @OA\Property(property="data", type="string", example=null),
     *         ),
     *     ),
     * )
     */
    public function userNotifications($user_id): JsonResponse
    {
        $userNotifications = Notification::where('user_id', $user_id)->latest()->get();
        if ($userNotifications) {
            return response()->json([
                'success' => true,
                'message' => 'List of Notification Data from User ID ' . $user_id,
                'data'    => $userNotifications
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User Notification Data History Not Found!',
                'data'    => null
            ], 404);
        }
    }
}
