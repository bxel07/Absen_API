<?php

namespace App\Http\Controllers\Auth\Google_auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Http\Redirector;
use function App\Http\Controllers\bcrypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class GAuthController_rev extends Controller
{
    public function redirectToGoogle(): Redirector|\Illuminate\Http\RedirectResponse
    {
        $clientId = env('GOOGLE_CLIENT_ID');
        $redirectUri = env('GOOGLE_REDIRECT');
        $url = "https://accounts.google.com/o/oauth2/auth";
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'email',
        ];

        return redirect("$url?" . http_build_query($params));
    }

    public function handleGoogleCallback(Request $request): JsonResponse
    {
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
