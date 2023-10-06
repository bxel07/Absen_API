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
     * @throws ValidationException
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
