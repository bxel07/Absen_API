<?php

namespace App\Http\Controllers\Attendance\History;

use App\Models\Attendances;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserAttendenceHistory
{
    // Method untuk mengambil riwayat absensi anggota.
    public function MemberLog(): JsonResponse
    {
        return $this->extracted();
    }

    // Method untuk mengambil riwayat absensi manajer.
    public function ManagerLog(): JsonResponse
    {
        return $this->extracted();
    }

    /**
     * Method internal untuk mengekstrak data absensi dan meresponsnya dalam format JSON.
     *
     * @return JsonResponse
     */
    private function extracted(): JsonResponse
    {
        $data = Auth::user();

        // Mengambil daftar absensi berdasarkan ID pengguna.
        $attendances = Attendances::where('user_id', $data->id);
        $clockInColumn = $attendances->pluck('clock_in');
        $clockOutColumn = $attendances->pluck('clock_out');

        // Mengembalikan data absensi dalam format JSON.
        return response()->json([
            'message' => 'Rekap Absensi',
            'user_id' => $data->id, // Mengganti $data menjadi $data->id
            'role_id' => $data->role_id,
            'fullname' => $data->fullname,
            'clock_in' => $clockInColumn,
            'clock_out' => $clockOutColumn
        ], 201);
    }
}
