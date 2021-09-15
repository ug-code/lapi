<?php

namespace App\Http\Controllers;

use App\Http\Request\Auth\AuthLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     ** path="/api/auth/login",
     *   tags={"auth"},
     *   summary="Get a JWT via given credentials",
     *
     *  @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *       name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *      )
     *   )
     * )
     *
     **/
    public function login(AuthLoginRequest $request)
    {
        $validated = $request->validated();

        // $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($validated)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     * path="/api/auth/me",
     *   tags={"auth"},
     *   summary="Get the authenticated User.",
    *   @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *      )
     *   )
     * )
     *
     **/
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).

     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     * path="/api/auth/logout",
     *   tags={"auth"},
     *   summary="Log the user out (Invalidate the token)..",
     *   @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *      )
     *   )
     * )
     *
     **/
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     * path="/api/auth/refresh",
     *   tags={"auth"},
     *   summary="Refresh a token.",
     *   @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *      )
     *   )
     * )
     *
     **/
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()
                    ->factory()
                    ->getTTL() * 60
        ]);
    }

}