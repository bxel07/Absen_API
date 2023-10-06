<?php

namespace App\Http\Controllers\Auth\Email_Verification;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class SendMailController extends Controller
{

     public function pageForgotPassword()
     {
         return view('forgot-password');
     }

    public function sendMailVerification(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(60);
        $otp = random_int(1111,9999);

        // Contoh penggunaan fasilitas email Laravel:
        $email = $request->input('email');

        Verification::updateOrCreate(
            ['email' => $email],
            ['otp' => $otp, 'token' => $token, 'expired_at' => Carbon::now()->addMinutes(1)]
        );

        // Kirim email
        Mail::send('subject-email', ['otp' => $otp], function ($message) use ($email) {
            $message->from('otakkanan@gmail.com', 'PT. OTAK KANAN');
            $message->to($email);
            $message->subject('Reset Password');
        });

        // Tampilkan pesan sukses atau pesan error jika ada
        return response()->json(['message' => 'Email send'], 200);
        //   return view('verify-otp', ['email' => $email]);
    }

    // public function pageResetPassword()
    // {
    //     return view('verify-otp');
    // }

    /**
     * @throws ValidationException
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');

        $verification = Verification::where('email', $email)->where('expired_at', '>=', Carbon::now())->first();

        if ($verification && $verification->expired_at >= Carbon::now()) {
            if ($verification->otp == $otp) {
                return response()->json(['message' => 'OTP valid'], 200);
            } else {
                return response()->json(['message' => 'OTP is invalid'], 400);
            }
        } else {
            return response()->json(['message' => 'OTP expired'], 400);
        }
    }

}
