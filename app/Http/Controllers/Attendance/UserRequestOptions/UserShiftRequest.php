<?php

namespace App\Http\Controllers\Attendance\UserRequestOptions;

use App\Http\Controllers\Controller;
use App\Models\ShiftRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserShiftRequest extends Controller
{
    /**
     * Process shift request for the authenticated user.
     *
     * @OA\Post(
     *     path="/api/shift-request",
     *     summary="Process shift request",
     *     description="Process shift request for the authenticated user. Requires the on date, old shift start and end times, new shift start and end times, and reason.",
     *     tags={"Shift Request"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="on_date", type="string", description="Date of the shift change."),
     *             @OA\Property(property="old_shift_start", type="string", description="Start time of the old shift."),
     *             @OA\Property(property="old_shift_end", type="string", description="End time of the old shift."),
     *             @OA\Property(property="new_shift_start", type="string", description="Start time of the new shift."),
     *             @OA\Property(property="new_shift_end", type="string", description="End time of the new shift."),
     *             @OA\Property(property="reason", type="string", description="Reason for the shift request."),
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
     *         description="Success: Shift request processed successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function ShiftRequestProcess(Request $request): JsonResponse
    {
        // Method untuk memproses permintaan perubahan shift pengguna
        $validator = Validator::make($request->all(), [
            'on_date' => 'required',
            'old_shift_start' => 'required',
            'old_shift_end' => 'required',
            'new_shift_start' => 'required',
            'new_shift_end' => 'required',
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Mohon lengkapi seluruh kolom input yang ada!',
                'data' => $validator->errors(),
            ], 422);
        } else {
            // Simpan data permintaan perubahan shift ke database
            $ShiftRequestData = ShiftRequest::create([
                'user_id' => Auth::user()->id,
                'on_date' => $request->on_date,
                'old_shift_start' => $request->old_shift_start,
                'old_shift_end' => $request->old_shift_end,
                'new_shift_start' => $request->new_shift_start,
                'new_shift_end' => $request->new_shift_end,
                'reason' => $request->reason,
            ]);

            /**
             * Entry to Approved Request
             */
            // Masukkan data ke Tabel 'approved_requests' dengan status 'pending'
            DB::table('approved_requests')->insert([
                'user_id' => Auth::user()->id,
                'shift_request_id' => $ShiftRequestData->id,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan Shift Request!',
                'data' => $ShiftRequestData,
            ], 422);
        }
    }
}
