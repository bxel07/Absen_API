<?php

namespace App\Http\Controllers\Auth\Google_auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Http\Redirector;
use function App\Http\Controllers\bcrypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class GAuthController_rev extends Controller
{
    /**
     * @OA\Get(
     *     path="/auth/google/login",
     *     summary="Redirect to Google for authentication",
     *     description="Redirects to Google for authentication using OAuth2.",
     *     tags={"Authentication login with google"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="string",
     *             example="https://accounts.google.com/o/oauth2/auth?client_id=YOUR_CLIENT_ID&redirect_uri=YOUR_REDIRECT_URI&response_type=code&scope=email"
     *         )
     *     )
     * )
     */
    public function redirectToGoogle(): JsonResponse
    {
        //Method: Mengarahkan ke Google untuk otentikasi
        $clientId = env('GOOGLE_CLIENT_ID');
        $redirectUri = env('GOOGLE_REDIRECT');
        $url = "https://accounts.google.com/o/oauth2/auth";
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'email',
        ];

        //return redirect("$url?" . http_build_query($params));
        $redirectUrl = "$url?" . http_build_query($params);

        return response()->json($redirectUrl);
    }

    /**
     * @OA\Get(
     *     path="/auth/google/callback",
     *     summary="Handle Google Callback",
     *     description="Handles the Google callback after authentication using OAuth2.",
     *     tags={"Authentication login with google"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function handleGoogleCallback(Request $request): JsonResponse
    {
        //Method: Menangani Callback Google
        $code = $request->input('code');
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri = env('GOOGLE_REDIRECT');

        $tokenUrl = "https://accounts.google.com/o/oauth2/token";
        $tokenData = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $response = Http::post($tokenUrl, $tokenData);

        $accessToken = $response->json()['access_token'];

        $userInfoUrl = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=$accessToken";
        $userInfo = Http::get($userInfoUrl)->json();

        // Cek apakah email pengguna sudah terdaftar di tabel users
        $existingUser = User::where('email', $userInfo['email'])->first();

        if ($existingUser) {
            // Login pengguna
            Auth::login($existingUser);
            $token = JWTAuth::fromUser($existingUser);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => auth()->user(),
                'expires_in' => auth()->factory()->getTTL() * 60 * 24
            ]);
        } else {
            return response()->json(['message' => 'Google login failed. Email not found in users table.'], 401);
        }
    }
}
