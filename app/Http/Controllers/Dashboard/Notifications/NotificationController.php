<?php

namespace App\Http\Controllers\Dashboard\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function allNotifications(): JsonResponse
    {
        $allNotifications = Notification::latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'List Data Notifikasi',
            'data'    => $allNotifications
        ], 200);
    }

    public function userNotifications($user_id): JsonResponse
    {
        $userNotifications = Notification::where('user_id', $user_id)->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'List Data Notifikasi User ID ' . $user_id,
            'data'    => $userNotifications
        ], 200);
    }
}
