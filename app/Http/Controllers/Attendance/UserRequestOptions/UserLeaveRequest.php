<?php

namespace App\Http\Controllers\Attendance\UserRequestOptions;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class UserLeaveRequest extends Controller
{
    public function LeaveRequestProcess(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'start_date' => 'required',
            'start_end' => 'required',
            'reason' => 'required',
            'delegations' => 'required',
            'upload_file' => 'required|file|mimetypes:application/pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mohon lengkapi seluruh kolom input yang ada!',
                'data' => $validator->errors(),
            ], 422);
        } else {
            $FileUpload = $request->file('upload_file');
            $FileUpload->storeAs('public/images', $FileUpload->hashName());
            $LeaveRequestData = LeaveRequest::create([
                'user_id' => Auth::user()->id,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'start_end' => $request->start_end,
                'reason' => $request->reason,
                'delegations' => $request->delegations,
                'upload_file' => $FileUpload->hashName(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan Leave Request!',
                'data' => $LeaveRequestData,
            ], 422);
        }
    }
}
