<?php

namespace App\Http\Controllers\Attendance\UserRequestOptions;

use App\Http\Controllers\Controller;
use App\Models\ScheduleShift;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserAttendanceRequest extends Controller
{
    private float $OfficeLatitude = -7.219900;
    private float $OfficeLongitude = 112.750069;
    private float $MaxRadius = 200.0;

    /**
     * Get schedule for the authenticated user.
     *
     * @OA\Get(
     *     path="/api/get-shift-schedules",
     *     summary="Get user's schedule",
     *     description="Retrieve the schedule of the authenticated user.",
     *     tags={"Attendance Request"},
     *     @OA\Response(
     *         response=201,
     *         description="Success: User schedule retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: Data not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="error", type="string"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function getSchedule (): JsonResponse
    {
        $user_id = Auth::user()->id;

      if (!$user_id) {
          return response()->json([
                  'success' => false,
                  'error' => "Data not found"
              ], 400);
      }
        /**
         * Getting schedule time
         */
        $results = DB::table('schedule_shift')
            ->select('shifts.name as shift_name', 'shifts.schedule_in', 'shifts.schedule_out')
            ->join('shifts', 'schedule_shift.shift_id', '=', 'shifts.id')
            ->join('schedules', 'schedule_shift.schedule_id', '=', 'schedules.id')
            ->join('users', 'schedule_shift.user_id', '=', 'users.id')
            ->where('users.id', $user_id)
            ->get();

        return response()->json([
           'success' => true,
           'data' => $results
        ],201);

    }



    /**
     * Clock in for user attendance.
     *
     * @OA\Post(
     *     path="/api/clock-in",
     *     summary="Clock in for user attendance",
     *     description="Clock in for user attendance at the office location. Requires proper shift, time, description, and location within a specified radius.",
     *     tags={"Attendance Request"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="shift", type="string", description="User's shift."),
     *             @OA\Property(property="clock_in", type="string", description="Clock-in time."),
     *             @OA\Property(property="description", type="string", description="Clock-in description."),
     *             @OA\Property(property="upload_file", type="string", format="binary", description="Image file for clock-in."),
     *             @OA\Property(property="point", type="array", @OA\Items(type="number"), description="User's current location [latitude, longitude]."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success: User clocked in successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Invalid input data.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: User location outside of the specified radius.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="null"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */

    public function ClockIn_Rev(Request $request): JsonResponse
    {
        $credential = Validator::make($request->all(), [
            'clock_in' => 'required',
            'description' => 'required',
            'upload_file' => 'required|image|mimes:png,jpg,jpeg',
            'point' => 'required',
        ]);
        /**
         * For Error input value when not complited
         */
        if ($credential->fails()) {
            return response()->json($credential->errors(), 422);
        }

        $PointUserLocations = $request->input('point', []);
        /**
         * Get explode latitude and logitude falue as string
         */
        $data = $this->User_Lat_Long($PointUserLocations);

        if($this->RadiusCalc($data->latitude, $data->longitude, $this->OfficeLatitude, $this->OfficeLongitude) <= $this->MaxRadius){
            $getAllRequest = $this->getArr($request);

            /**
             * get user shift time id
             */
            $schedule_shift = ScheduleShift::where('user_id' ,Auth::user()->id)->pluck('id');

            /**
             * Insert to DB
             */
            $RequestId = DB::table('attendance_requests')->insertGetId([
                'user_id' => Auth::user()->id,
                'shift' => $schedule_shift->first(),
                'clock_in' => $getAllRequest['clock_in'],
                'description' => $getAllRequest['description'],
                'upload_file' => $getAllRequest['upload_file'],
                'point' => DB::raw('ST_PointFromText("POINT(' . $getAllRequest['point'][0] . ' ' . $getAllRequest['point'][1] . ')")'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            /**
             * Entry to Approved Request
             */
            DB::table('approved_requests')->insert([
                'user_id' => Auth::user()->id,
                'attendance_request_id' => $RequestId,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' =>'Anda Berhasil Clock in !',
                'data'    => $getAllRequest
            ], 201);



        } else {
            return response()->json([
                'success' => true,
                'message' =>'Anda tidak dapat melakukan attendance di lokasi ini!',
                'data'    => null
            ], 201);
       }
    }


    /**
     * Clock out for user attendance.
     *
     * @OA\Post(
     *     path="/api/clock-out",
     *     summary="Clock out for user attendance",
     *     description="Clock in for user attendance at the office location. Requires proper shift, time, description, and location within a specified radius.",
     *     tags={"Attendance Request"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="shift", type="string", description="User's shift."),
     *             @OA\Property(property="clock_out", type="string", description="Clock-out time."),
     *             @OA\Property(property="description", type="string", description="Clock-in description."),
     *             @OA\Property(property="upload_file", type="string", format="binary", description="Image file for clock-in."),
     *             @OA\Property(property="point", type="array", @OA\Items(type="number"), description="User's current location [latitude, longitude]."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success: User clockout in successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Invalid input data.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: User location outside of the specified radius.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="null"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function ClockOut(Request $request): JsonResponse
    {
        $credential = Validator::make($request->all(), [
            'clock_out' => 'required',
            'description' => 'required',
            'upload_file' => 'required|image|mimes:png,jpg,jpeg',
            'point' => 'required',
        ]);

        /**
         * For Error input value when not complited
         */
        if ($credential->fails()) {
            return response()->json($credential->errors(), 422);
        }

        $PointUserLocations = $request->input('point', []);

        /**
         * Get explode latitude and logitude falue as string
         */
        $data = $this->User_Lat_Long($PointUserLocations);
        if($this->RadiusCalc($data->latitude, $data->longitude, $this->OfficeLatitude, $this->OfficeLongitude) <= $this->MaxRadius){
            $getAllRequest = $this->getArr($request);
            /**
             * get user shift time id
             */
            $schedule_shift = ScheduleShift::where('user_id' ,Auth::user()->id)->pluck('id');

            /**
             * Insert to DB
             */
            $RequestId = DB::table('attendance_requests')->insertGetId([
                'user_id' => Auth::user()->id,
                'shift' => $schedule_shift->first(),
                'clock_out' => $getAllRequest['clock_out'],
                'description' => $getAllRequest['description'],
                'upload_file' => $getAllRequest['upload_file'],
                'point' => DB::raw('ST_PointFromText("POINT(' . $getAllRequest['point'][0] . ' ' . $getAllRequest['point'][1] . ')")'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            /**
             * Entry to Approved Request
             */
            DB::table('approved_requests')->insert([
                'user_id' => Auth::user()->id,
                'attendance_request_id' => $RequestId,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return response()->json([
                'success' => true,
                'message' =>'Anda berhasil ClockOut!!',
                'data'    => $getAllRequest
            ], 201);
        } else {
            return response()->json([
                'success' => true,
                'message' =>'Anda tidak dapat melakukan attendance di lokasi ini!',
                'data'    => null
            ], 201);
        }
    }

    private function RadiusCalc(float $lat1, float $lon1, float $lat2, float $lon2): float|int|null
    {
        $a = 6378137; // Earth's semi-major axis in meters
        $b = 6356752.3142; // Earth's semi-minor axis in meters
        $f = 1 / 298.257223563; // Earth's flattening

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $L = $lon2Rad - $lon1Rad;
        $U1 = atan((1 - $f) * tan($lat1Rad));
        $U2 = atan((1 - $f) * tan($lat2Rad));
        $sinU1 = sin($U1);
        $cosU1 = cos($U1);
        $sinU2 = sin($U2);
        $cosU2 = cos($U2);

        $lambda = $L;
        $lambdaP = 2 * M_PI;
        $iterLimit = 100;

        while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0) {
            $sinLambda = sin($lambda);
            $cosLambda = cos($lambda);
            $sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) +
                ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) *
                ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));
            if ($sinSigma == 0) {
                return 0; // Co-incident points
            }
            $cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
            $sigma = atan2($sinSigma, $cosSigma);
            $sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
            $cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
            $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;

            $C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
            $lambdaP = $lambda;
            $lambda = $L + (1 - $C) * $f * $sinAlpha *
                ($sigma + $C * $sinSigma *
                    ($cos2SigmaM + $C * $cosSigma *
                        (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
        }

        if ($iterLimit == 0) {
            return null; // Formula failed to converge
        }

        $uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
        $A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
        $B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
        $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 *
                ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) -
                    $B / 6 * $cos2SigmaM *
                    (-3 + 4 * $sinSigma * $sinSigma) *
                    (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));
        return $b * $A * ($sigma - $deltaSigma);
    }

    private function User_Lat_Long(array $userLoc): object
    {
        return (object) ['latitude' => (float)$userLoc[0], 'longitude' => (float)$userLoc[1]];
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getArr(Request $request): array
    {
        $selfie = $request->file('upload_file');
        $selfie->storeAs('public/attendance', $selfie->hashName());
        $url = Storage::url('public/images/' . $selfie->hashName());

        $getAllRequest = $request->all();
        $getAllRequest['upload_file'] = $url;

        return $getAllRequest;
    }
}
