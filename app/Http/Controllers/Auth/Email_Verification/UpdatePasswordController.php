<?php

namespace App\Http\Controllers\Auth\Email_Verification;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UpdatePasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * @OA\Post(
     *     path="/api/update-password",
     *     summary="Update user's password",
     *     description="Update the user's password with a new one.",
     *     tags={"Password Update"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email", description="User's email."),
     *             @OA\Property(property="new_password", type="string", format="password", description="New password for the user."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Password updated.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Email not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Validation errors.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *         ),
     *     ),
     * )
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email',
            'new_password' => 'required|min:6',
        ]);

        $email = $request->input('email');
        $newPassword = Hash::make($request->input('new_password'));

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        $user->update(['password' => $newPassword]);

        return response()->json(['message' => 'Kata sandi berhasil diubah']);
    }
}
