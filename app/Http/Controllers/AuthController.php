<?php

namespace App\Http\Controllers;

use App\Http\Middleware\JwtMiddleware;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;

class AuthController extends Controller
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login',
                                                'register']),
            new Middleware(JwtMiddleware::class, except: ['login',
                                                          'register']),
        ];
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $request->validated();

        $user           = new User();
        $user->fullname = $request->get('fullname');
        $user->email    = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return response()->json(['status'  => true,
                                 'message' => 'User created successfully',
                                 'data'    => $user]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();

        $credentials = $request->only('email', 'password');
        $token       = auth()->attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function userList(): JsonResponse
    {
        $user = User::paginate(10);
        return response()->json(['status' => true,
                                 'data'   => $user]);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
