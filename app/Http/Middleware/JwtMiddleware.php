<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware

{
    const USER_NOT_FOUND = "'User Not Found'";

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()
                           ->authenticate();
            if (!$user) throw new Exception(self::USER_NOT_FOUND);

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'data'   => null,
                    'status' => false,
                    'err_'   => [
                        'message' => 'Token Invalid',
                        'code'    => 1
                    ]
                ], 401);

            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json([
                    'data'   => null,
                    'status' => false,
                    'err_'   => [
                        'message' => 'Token Expired',
                        'code'    => 1

                    ]

                ], 401);

            } else {
                if ($e->getMessage() === self::USER_NOT_FOUND) {
                    return response()->json([
                        "data"   => null,
                        "status" => false,
                        "err_"   => [
                            "message" => self::USER_NOT_FOUND,
                            "code"    => 1
                        ]
                    ], 401);
                }

                return response()->json([
                    'data'   => null,
                    'status' => false,
                    'err_'   => [
                        'message' => 'Authorization Token not found',
                        'code'    => 1
                    ]
                ], 401);
            }

        }

        return $next($request);

    }

}
