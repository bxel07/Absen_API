<?php

namespace App\Http\Controllers\Attendance\UserRequestOptions;

use App\Http\Controllers\Controller;
use App\Models\ShiftRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserShiftRequest extends Controller
{
    public function ShiftRequestProcess(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'shift' => 'required',
            'shift_type' => 'required',
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mohon lengkapi seluruh kolom input yang ada!',
                'data' => $validator->errors(),
            ], 422);
        } else {
            $ShiftRequestData = ShiftRequest::create([
                'user_id' => Auth::user()->id,
                'shift' => $request->shift,
                'shift_type' => $request->shift_type,
                'reason' => $request->reason,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan Shift Request!',
                'data' => $ShiftRequestData,
            ], 422);
        }
    }
}
