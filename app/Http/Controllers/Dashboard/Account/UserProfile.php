<?php

/**
 * @license Apache 2.0
 */

namespace App\Http\Controllers\Dashboard\Account;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserProfile extends Controller
{
    /**
     * Retrieve user information.
     *
     * @OA\Get(
     *     path="/api/user-info",
     *     summary="Get user info",
     *     description="Retrieve information about the logged-in user. Requires authentication.",
     *     tags={"Profile"},
     *     @OA\Response(
     *         response=200,
     *         description="Success: User information retrieved.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: User data not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */

    public function show(): JsonResponse
    {
        // Mendapatkan user yang sedang login.
        $GetUser = Auth::user()->id;
        // Memeriksa apakah user ID yang diberikan ditemukan.
        if (!$GetUser) {
            // Jika user ID tidak ditemukan, kembalikan respon JSON dengan pesan kesalahan.
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data User Tidak Ditemukan!',
                ],
                404
            );
        }
        // Jika user ID ditemukan, kembalikan respon JSON dengan data user.
        return response()->json([
            'success' => true,
            'message' => 'Data user telah ditemukan',
            'data' => Auth::user()
        ], 200);
    }

    /**
     * Update the user's profile information.
     *
     * @param  Request  $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/update-profile",
     *     summary="Update the user's profile information",
     *     description="Update the user's profile information based on the provided request data.",
     *     tags={"Profile"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="fullname", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="date_of_birth", type="string"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="contact", type="string"),
     *                 @OA\Property(property="religion", type="string"),
     *                 @OA\Property(property="image_profile", type="string", format="binary"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Data update failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Required fields missing",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     * )
     */
    public function update(Request $request): JsonResponse
    {
        // Mendapatkan user ID yang sedang login.
        $userprofile = Auth::user()->id;
        // Membuat validasi untuk data yang dikirimkan.
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email' => 'required|email|unique:users,email,' . $userprofile,
            'date_of_birth' => 'required',
            'gender' => 'required',
            'contact' => 'required',
            'religion' => 'required',
        ]);

        // Jika validasi gagal, kembalikan respon JSON dengan pesan kesalahan.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua Kolom Wajib Diisi!',
                'data'   => $validator->errors()
            ], 401);
        } else {
            // Cari user berdasarkan ID.
            $user = User::find($userprofile);

            // Jika user tidak ditemukan, kembalikan respon JSON dengan pesan kesalahan.
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan!',
                ], 404);
            }
            // Buat array data yang akan diupdate.
            $updateData = [
                'fullname' => $request->input('fullname'),
                'email' => $request->input('email'),
                'date_of_birth' => $request->input('date_of_birth'),
                'gender' => $request->input('gender'),
                'contact' => $request->input('contact'),
                'religion' => $request->input('religion'),
            ];
            // Jika ada file gambar profil yang dikirimkan, upload file tersebut dan simpan URL-nya ke dalam array `updateData`.
            if ($request->hasFile('image_profile')) {
                $image_profile = $request->file('image_profile');
                $image_hash  = $image_profile->hashName();
                $imagePath = 'public/profile/' . $image_hash;
                $image_profile->storeAs('public/profile', $image_hash);
                $imageUrl = asset($imagePath);
                $updateData['image_profile'] = $imageUrl;
            }
            // Update data user.
            $update = $user->update($updateData);
            // Jika update data berhasil, kembalikan respon JSON dengan pesan sukses.
            if ($update) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Diupdate!',
                ], 201);
            } else {
                // Jika update data gagal, kembalikan respon JSON dengan pesan kesalahan.
                return response()->json([
                    'success' => false,
                    'message' => 'Data Gagal Diupdate!',
                ], 400);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    // Mengubah kata sandi pengguna.
    public function changePassword(Request $request): JsonResponse
    {
        // Dapatkan ID pengguna yang sedang login.
        $userid = Auth::user()->id;
        // Lakukan validasi untuk data yang dikirimkan.
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'repeat_new_password' => 'required|min:6',
        ]);

        // Cari pengguna berdasarkan ID.
        $user = User::find($userid);
        // Periksa apakah kata sandi lama yang dimasukkan benar.
        if (!Hash::check($request->input('old_password'), $user->password)) {
            // Jika kata sandi lama salah, kembalikan respon JSON dengan pesan kesalahan.
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama salah'
            ]);
        }
        // Periksa apakah kata sandi baru dan kata sandi ulang sama.
        if ($request->input('new_password') !== $request->input('repeat_new_password')) {
            // Jika kata sandi baru dan kata sandi ulang tidak sama, kembalikan respon JSON dengan pesan kesalahan.
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi tidak sama'
            ]);
        }
        // Enkripsi kata sandi baru.
        $newPassword = Hash::make($request->input('new_password'));

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }
        // Update kata sandi pengguna.
        $user->update(['password' => $newPassword]);
        // Jika update kata sandi berhasil, kembalikan respon JSON dengan pesan sukses.
        return response()->json(['message' => 'Kata sandi berhasil diubah']);
    }
}
