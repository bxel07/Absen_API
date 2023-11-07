<?php

namespace App\Http\Controllers\Attendance\UserRequestOptions;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Carbon;


class AttendanceStatusController extends Controller
{
    /**
     * @var array
     * untuk mengambil kemungkinan data
     */
    private array $DetailDara= [];

    public function index(): JsonResponse
    {

        return response()->json($this->UserDetailShift());
    }

    private function UserDetailShift(): array
    {
        $users = User::all();

        $responseData = [];

        foreach ($users as $user) {

            /**
             * Mengambil data terkait data clock-in
             */

            $CurrentDate = date('Y-m-d');
            $userShiftSchedules = $user->schedule_shift()->with('Shift')->get();
            $userAttendance  = $user->attendance_request()->whereDate('created_at', $CurrentDate)->latest('created_at')->first(['id', 'clock_in', 'created_at']);
            $userApprovedAttendance  = $user->approved_request()->whereDate('created_at', $CurrentDate)->latest('created_at')->first();
            $userpositions = $user->employment()->with('department.jobPosition')->get();

            /**
             * Mengambil data terkait perizinan
             */
            $userLeaveRequests = $user->leave_request()->get('id');

            /**
             * mendapatkan shift value
             */
            $shift = $userShiftSchedules->map(function ($item){
                return [
                    'name' =>   optional($item->shift)->name,
                    'start' =>   optional($item->shift)->schedule_in,
                ];
            });

   //passing data array seperlunya
            $userData = [
                'user' => ['name' => $user->fullname, 'profile' => $user->image_profile],
                'positions' => $userpositions[0]->department->jobPosition->name,
                'shifts' => $shift,
                'attendance' => $userAttendance,
                'approved_attendance' => $userApprovedAttendance,
                'leave' => $userLeaveRequests,
            ];

            $responseData[] = $userData;
        }

        return $responseData;
    }


    private function AlphaChecker()
    {
        /**
         * Get Available data
         */
        $UserAttendanceData = $this->UserDetailShift();

        /**
         * param for user data
         */

        $userStatusLate = [];
        $userStatusAlpha = [];

        /**
         * Generate date
         */

        $currentdate = Carbon::now();
        $currentdates = $currentdate->format('Y-m-d');
        $objectCurrentDate = Carbon::parse($currentdates);
        foreach ($UserAttendanceData as $users ){
            $userAttendanceDate = $users['approved_attendance']['created_at'];
            $userAttendanceDateConvert = Carbon::parse($userAttendanceDate);
            $userValidAttendanceDate = $userAttendanceDateConvert->format('Y-m-d');
            $objectuserValidAttendanceDate = $userAttendanceDateConvert->format('Y-m-d');
            if($users['attendance']['clock_in'] !== null){
                $shiftTime = $users['shifts'][0]['start'];
                $clockIn = $users['attendance']['clock_in'];
                $accAttendance = $users['approved_attendance']['status'];

                if (strtotime($clockIn) >= strtotime($shiftTime) && $objectCurrentDate->eq() ){

                    $userStatusLate [] = [
                        'user' => $users['user']['name'],
                        'positions' => $users['positions'],
                        'profile' => $users['user']['profile'],
                        'status' => 'Late'
                    ];
                }

            } else {
                return response()->json([
                   'success' => false,
                   'message' => 'User Alpha Today'
                ],200);
            }
        }

    }

    private function PermitChecker() {

    }


}
