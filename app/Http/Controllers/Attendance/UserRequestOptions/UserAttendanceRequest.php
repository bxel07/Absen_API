<?php

namespace App\Http\Controllers\Attendance\UserRequestOptions;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserAttendanceRequest extends Controller
{
    private float $OfficeLatitude = -7.219900;
    private float $OfficeLongitude = 112.750069;
    private float $MaxRadius = 200.0;
    public function ClockIn_Rev(Request $request): JsonResponse
    {
        $credential = Validator::make($request->all(), [
            'shift' => 'required',
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
             * Insert to DB
             */


            DB::table('attendance_requests')->insert([
                'user_id' => Auth::user()->id,
                'shift' => $getAllRequest['shift'],
                'clock_in' => $getAllRequest['clock_in'],
                'description' => $getAllRequest['description'],
                'upload_file' => $getAllRequest['upload_file'],
                'point' => DB::raw('ST_PointFromText("POINT(' . $getAllRequest['point'][0] . ' ' . $getAllRequest['point'][1] . ')")')
            ]);

            return response()->json([
                'success' => true,
                'message' =>'Anda Berhasil Clock_in',
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
    public function ClockOut(Request $request): JsonResponse
    {
        $credential = Validator::make($request->all(), [
            'shift' => 'required',
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
             * Insert to DB
             */
            DB::table('attendance_requests')->insert([
                'user_id' => Auth::user()->id,
                'shift' => $getAllRequest['shift'],
                'clock_out' => $getAllRequest['clock_out'],
                'description' => $getAllRequest['description'],
                'upload_file' => $getAllRequest['upload_file'],
                'point' => DB::raw('ST_PointFromText("POINT(' . $getAllRequest['point'][0] . ' ' . $getAllRequest['point'][1] . ')")') ]);
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

        $getAllRequest = $request->all();
        $getAllRequest['upload_file'] = $selfie->hashName();

        return $getAllRequest;
    }
}
