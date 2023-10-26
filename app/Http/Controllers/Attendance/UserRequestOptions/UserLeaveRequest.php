<?php

namespace App\Http\Controllers\Attendance\UserRequestOptions;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserLeaveRequest extends Controller
{
    /**
     * Process leave request for the authenticated user.
     *
     * @OA\Post(
     *     path="/api/leave-request",
     *     summary="Process leave request",
     *     description="Process leave request for the authenticated user. Requires the type, start date, end date, reason, and upload of a PDF document.",
     *     tags={"Leave Request"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="type", type="string", description="Type of leave request."),
     *             @OA\Property(property="start_date", type="string", description="Start date of the leave."),
     *             @OA\Property(property="start_end", type="string", description="End date of the leave."),
     *             @OA\Property(property="reason", type="string", description="Reason for the leave request."),
     *             @OA\Property(property="upload_file", type="string", format="binary", description="PDF document for the leave request."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Incomplete input data.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Leave request processed successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function LeaveRequestProcess(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'start_date' => 'required',
            'start_end' => 'required',
            'reason' => 'required',
            'upload_file' => 'required|file|mimetypes:application/pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mohon lengkapi seluruh kolom input yang ada!',
                'data' => $validator->errors(),
            ], 422);
        } else {

            /**
             * Save Document Permission
             */

            $FileUpload = $request->file('upload_file');
            $FileUpload->storeAs('public/images', $FileUpload->hashName());

            $LeaveRequestData = LeaveRequest::create([
                'user_id' => Auth::user()->id,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'start_end' => $request->start_end,
                'reason' => $request->reason,
                'delegations' => $request->delegations ?? null,
                'upload_file' => $FileUpload->hashName(),
            ]);

            /**
             * Entry to Approved Request
             */
            DB::table('approved_requests')->insert([
                'user_id' => Auth::user()->id,
                'leave_request_id' => $LeaveRequestData->id,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave Request Terkirim  ',
            ], 422);
        }
    }
}
