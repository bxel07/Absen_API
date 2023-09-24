<?php

namespace App\Http\Controllers\Auth\Google_auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use function App\Http\Controllers\bcrypt;

class GAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $query = http_build_query([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('GOOGLE_REDIRECT'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
        ]);

        return redirect("https://accounts.google.com/o/oauth2/auth?$query");
    }

    public function handleGoogleCallback(Request $request)
    {
        $verifySSL = env('VERIFY_SSL');
        $client = new Client([
            'verify' => $verifySSL,
        ]);
        $response = $client->post('https://accounts.google.com/o/oauth2/token', [
            'form_params' => [
                'code' => $request->input('code'),
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_REDIRECT'),
                'grant_type' => 'authorization_code',
            ],
        ]);

        $access_token = json_decode((string)$response->getBody(), true)['access_token'];

        $user_info = $client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
            ],
        ]);

        // Proses informasi pengguna yang diperoleh dari Google di sini.
        $google_user_data = json_decode($user_info->getBody(), true);
        $user = User::where('google_id', $google_user_data['sub'])->first();

        if (!$user) {
            $user = new User();
            $user->google_id = $google_user_data['sub'];
        }
        $user->fullname = $google_user_data['name'];
        $user->email = $google_user_data['email'];
        $user->role_id = 1;

        // Menentukan jenis login berdasarkan adanya google_id
        $isGoogleLogin = isset($user->google_id) && !empty($user->google_id);

        if ($isGoogleLogin) {
            $user->password = ''; // Kolom password diisi dengan nilai kosong
        } else {
            // Jika ini login normal, ambil kata sandi dari input pengguna
            $password = $request->input('password'); // Gantilah dengan input sesuai dengan nama input Anda
            $user->password = bcrypt($password); // Kolom password dienkripsi
        }

        $user->save();
        return $user_info->getBody();
    }
}
