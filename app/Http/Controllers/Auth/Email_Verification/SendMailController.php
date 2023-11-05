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
        // Method: Menampilkan halaman form "forgot password"
        return view('forgot-password');
    }

    /**
     * Send email verification with OTP for password reset.
     *
     * @OA\Post(
     *     path="/api/send-mail",
     *     summary="Send email verification for password reset",
     *     description="Send an email verification containing OTP for password reset.",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email", description="User's email."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Email sent.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Invalid email or other validation errors.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *         ),
     *     ),
     * )
     */
    public function sendMailVerification(Request $request): JsonResponse
    {
        //Method: Mengirim verifikasi email dengan OTP untuk reset password
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(60);
        $otp = random_int(1111, 9999);

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
     * Verify the OTP sent to the user's email.
     *
     * @OA\Post(
     *     path="/api/verify-otp",
     *     summary="Verify OTP for password reset",
     *     description="Verify the OTP for the password reset process.",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email", description="User's email."),
     *             @OA\Property(property="otp", type="integer", description="OTP sent to the user's email."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: OTP verified.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: Invalid OTP or expired OTP.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     * )
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        //Method: Memverifikasi OTP yang dikirim ke email pengguna.
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
