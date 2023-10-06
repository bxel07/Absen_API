<?php

namespace App\Http\Controllers\Attendance;
use App\Models\Attendances;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AttenderController
{

    private float $OfficeLatitude = -7.219900;
    private float $OfficeLongitude = 112.750069;
    private float $MaxRadius = 200.0;

    public function ClockIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required',
            'shift_id' => 'required',
            'user_id' => 'required',
            'clock_in' => 'required',
            'photo' => 'required|image|mimes:png,jpg,jpeg',
            'shift_schedule' => 'required',
            'shift' => 'required',
            'location' => 'required',
            'notes' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data input harus dilengkapi!',
                'data' => $validator->errors(),
            ], 422);
        } else {
            // Koordinat kantor yang valid (Acuan Lokasi: Graha Pena)
            $officeLatitude = -7.219900;
            $officeLongitude = 112.750069;

            // Jarak maksimum yang diperbolehkan dari kantor (100m)
            $maxDistance = 0.10;

            // Mendapatkan nilai lokasi dari input pengguna
            $userLocation = $request->input('location');

            // Memisahkan nilai latitude dan longitude dari lokasi pengguna
            list($userLatitude, $userLongitude) = explode(',', $userLocation);

            // Memeriksa apakah pengguna berada dalam jarak yang diizinkan dari kantor
            if ($this->isWithinOfficeLocation($userLatitude, $userLongitude, $officeLatitude, $officeLongitude, $maxDistance)) {
                $selfie = $request->file('photo');
                $selfie->storeAs('public/clock_in', $selfie->hashName());

                $getAllRequest = $request->all();
                $getAllRequest['photo'] = $selfie->hashName();
                $attendance = Attendances::create($getAllRequest);


                return response()->json([
                    'success' => true,
                    'message' => 'Selamat, Anda berhasil melakukan attendance!',
                    'data' => $attendance,
                ], 201);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Anda tidak dapat melakukan attendance di lokasi ini!',
                    'data' => null,
                ], 201);
            }
        }
    }

    public function ClockIn_Rev(Request $request):JsonResponse{
        $credential = Validator::make($request->all(), [
            'schedule_id' => 'required',
            'shift_id' => 'required',
            'user_id' => 'required',
            'clock_in' => 'required',
            'photo' => 'required|image|mimes:png,jpg,jpeg',
            'shift_schedule' => 'required',
            'shift' => 'required',
            'location' => 'required',
            'notes' => 'required',
        ]);

        /**
         * For Error input value when not complited
         */
        if ($credential->fails()) {
            return response()->json($credential->errors(), 422);
        }

        $PointUserLocations = $request->input('location', []);

        /**
         * Get explode latitude and logitude falue as string
         */
        $data = $this->User_Lat_Long($PointUserLocations);
        if($this->RadiusCalc($data->latitude, $data->longitude, $this->OfficeLatitude, $this->OfficeLongitude) <= $this->MaxRadius){
            $getAllRequest = $this->getArr($request);
            /**
             * Insert to DB
             */
            DB::table('attendances')->insert([
                'schedule_id' => $getAllRequest['schedule_id'],
                'shift_id' => $getAllRequest['shift_id'],
                'user_id' => $getAllRequest['user_id'],
                'clock_in' => $getAllRequest['clock_in'],
                'photo' => $getAllRequest['photo'],
                'shift_schedule' => $getAllRequest['shift_schedule'],
                'shift' => $getAllRequest['shift'],
                'location' => DB::raw('ST_PointFromText("POINT(' . $getAllRequest['location'][0] . ' ' . $getAllRequest['location'][1] . ')")'),
                'notes' => $getAllRequest['notes'],
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
            'schedule_id' => 'required',
            'shift_id' => 'required',
            'user_id' => 'required',
            'clock_out' => 'required',
            'photo' => 'required|image|mimes:png,jpg,jpeg',
            'shift_schedule' => 'required',
            'shift' => 'required',
            'location' => 'required',
            'notes' => 'required',
        ]);

        /**
         * For Error input value when not complited
         */
        if ($credential->fails()) {
            return response()->json($credential->errors(), 422);
        }

        $PointUserLocations = $request->input('location', []);

        /**
         * Get explode latitude and logitude falue as string
         */
        $data = $this->User_Lat_Long($PointUserLocations);
        if($this->RadiusCalc($data->latitude, $data->longitude, $this->OfficeLatitude, $this->OfficeLongitude) <= $this->MaxRadius){
            $getAllRequest = $this->getArr($request);
            /**
             * Insert to DB
             */
            DB::table('attendances')->insert([
                'schedule_id' => $getAllRequest['schedule_id'],
                'shift_id' => $getAllRequest['shift_id'],
                'user_id' => $getAllRequest['user_id'],
                'clock_out' => $getAllRequest['clock_out'],
                'photo' => $getAllRequest['photo'],
                'shift_schedule' => $getAllRequest['shift_schedule'],
                'shift' => $getAllRequest['shift'],
                'location' => DB::raw('ST_PointFromText("POINT(' . $getAllRequest['location'][0] . ' ' . $getAllRequest['location'][1] . ')")'),
                'notes' => $getAllRequest['notes'],
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

    private function isWithinOfficeLocation(string $userLatitude, string $userLongitude, float $officeLatitude, float $officeLongitude, float $maxDistance): bool
    {
        // Radius Bumi dalam kilometer
        $earthRadius = 6371;
        $userLatRad = deg2rad($userLatitude);
        $userLngRad = deg2rad($userLongitude);
        $officeLatRad = deg2rad($officeLatitude);
        $officeLngRad = deg2rad($officeLongitude);

        // Haversine formula
        $deltaLat = $officeLatRad - $userLatRad;
        $deltaLng = $officeLngRad - $userLngRad;
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($userLatRad) * cos($officeLatRad) *
            sin($deltaLng / 2) * sin($deltaLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c *1000;

        return $distance <= $maxDistance;
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
        $selfie = $request->file('photo');
        $selfie->storeAs('public/clock_out', $selfie->hashName());

        $getAllRequest = $request->all();
        $getAllRequest['photo'] = $selfie->hashName();

        return $getAllRequest;
    }

}
