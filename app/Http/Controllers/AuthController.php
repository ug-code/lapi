<?php

namespace App\Http\Controllers;

use App\Http\Middleware\JwtMiddleware;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

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
        $user = $this->authService->register($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'User created successfully',
            'data'    => $user
        ]);
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
        
        $token = $this->authService->login($credentials);
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->authService->createTokenResponse($token));
    }

    public function userList(): JsonResponse
    {
        $users = $this->authService->getUserList();
        return response()->json([
            'status' => true,
            'data'   => $users
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json($this->authService->getAuthenticatedUser());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $token = $this->authService->refreshToken();
        return response()->json($this->authService->createTokenResponse($token));
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json($this->authService->createTokenResponse($token));
    }
}
