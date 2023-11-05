<?php

namespace App\Http\Controllers\Auth\JWT_Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and get the JWT token",
     *     description="Authenticate the user and return a JWT token for authentication.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="expires_in", type="integer", example="2880"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *         ),
     *     ),
     * )
     * @throws ValidationException
     */
    /**
     * @OA\Schema(
     *     schema="User",
     *     title="User",
     *     required={"id", "name", "email"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example="1"
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="John Doe"
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         example="johndoe@example.com"
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        //Method: Login dan Dapatkan JWT dengan kredensial yang diberikan.
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $this->jsonResponse($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/me",
     *     summary="Get the authenticated user",
     *     description="Retrieve the authenticated user's information.",
     *     tags={"Authentication"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */
    public function me(): JsonResponse
    {
        //Method: Dapatkan Pengguna yang terotentikasi.
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Log the user out",
     *     description="Invalidate the current token and log the user out.",
     *     tags={"Authentication"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out"),
     *         ),
     *     ),
     * )
     */
    public function logout(): JsonResponse
    {
        //Method: Logout pengguna (Invalidate token).
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Refresh a token",
     *     description="Refresh the current token and return a new token.",
     *     tags={"Authentication"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="expires_in", type="integer", example="2880"),
     *         ),
     *     ),
     * )
     */
    public function refresh(): JsonResponse
    {
        //Method: Perbarui token.
        return $this->jsonResponse(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function jsonResponse($token): JsonResponse
    {
        //Method: Dapatkan struktur array token.
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => auth()->user(),
            'expires_in'   => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}
