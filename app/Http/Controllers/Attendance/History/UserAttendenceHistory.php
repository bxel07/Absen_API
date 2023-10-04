<?php

namespace App\Http\Controllers\Attendance\History;
use App\Models\Attendances;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserAttendenceHistory
{
    public function MemberLog(): JsonResponse
    {
        return $this->extracted();
    }

    public function ManagerLog(): JsonResponse
    {
        return $this->extracted();
    }

    /**
     * @return JsonResponse
     */
    private function extracted(): JsonResponse
    {
        $data = Auth::user();

        $attendances = Attendances::where('user_id', $data->id);
        $clockInColumn = $attendances->pluck('clock_in');
        $clockOutColumn = $attendances->pluck('clock_out');


        return response()->json([
            'message' => 'Rekap Absensi',
            'user_id' => $data,
            'role_id' => $data->role_id,
            'fullname' => $data->fullname,
            'clock_in' => $clockInColumn,
            'clock_out' => $clockOutColumn
        ], 201);
    }

}
