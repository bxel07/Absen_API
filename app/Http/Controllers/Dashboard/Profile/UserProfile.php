<?php
/**
 * @license Apache 2.0
 */
namespace App\Http\Controllers\Dashboard\Profile;

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
        $GetUser = Auth::user()->id;

        if(!$GetUser) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data User Tidak Ditemukan!',
                ], 404
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data user telah ditemukan',
            'data' => Auth::user()
        ],200);

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
    public function update(Request $request):JsonResponse
    {
        $userprofile = Auth::user()->id;

        $validator = Validator::make($request->all(),[
            'fullname' => 'required',
            'email' => 'required|email|unique:users,email,'.$userprofile,
            'date_of_birth' => 'required',
            'gender' =>'required',
            'contact' => 'required',
            'religion' => 'required',
        ]);


        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Semua Kolom Wajib Diisi!',
                'data'   => $validator->errors()
            ],401);
        } else {
            $user = User::find($userprofile);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan!',
                ], 404);
            }

            $updateData = [
                'fullname' => $request->input('fullname'),
                'email' => $request->input('email'),
                'date_of_birth' => $request->input('date_of_birth'),
                'gender' => $request->input('gender'),
                'contact' => $request->input('contact'),
                'religion' => $request->input('religion'),
            ];

            if ($request->hasFile('image_profile')) {
                $image_profile = $request->file('image_profile');
                $image_hash  = $image_profile->hashName();
                $imagePath = 'public/profile/'.$image_hash;
                $image_profile->storeAs('public/profile', $image_hash);
                $imageUrl = asset($imagePath);
                $updateData['image_profile'] = $imageUrl;
            }

            $update = $user->update($updateData);

            if ($update) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Diupdate!',
                ], 201);
            } else {
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
    public function changePassword(Request $request): JsonResponse
    {
        $userid = Auth::user()->id;
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'repeat_new_password' => 'required|min:6',
        ]);

        $user = User::find($userid);
        if (!Hash::check($request->input('old_password'), $user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama salah'
            ]);
        }

        if ($request->input('new_password') !== $request->input('repeat_new_password')){
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi tidak sama'
            ]);
        }
        $newPassword = Hash::make($request->input('new_password'));



        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        $user->update(['password' => $newPassword]);

        return response()->json(['message' => 'Kata sandi berhasil diubah']);
    }
}
