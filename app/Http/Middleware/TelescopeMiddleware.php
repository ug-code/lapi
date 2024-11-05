<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TelescopeMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
            $token    = $request->input('token');
            if(!$token){
                $token   = $request->cookie('token');
            }

            $payload = JWTAuth::setToken($token)->getPayload();
            $exp     = $payload->get('exp');
            $minutes = floor(($exp - time()) / 60);
            $response->withCookie(cookie('token', $token, $minutes));

            $user  = JWTAuth::parseToken()->authenticate();
            $email = $user->email ?? null;
            if ($email == "tmp@tmp.com.tr") {
                return $response;
            } else {
                return response()->json(['error' => 'Token not valid'], 401);
            }


        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not valid'], 401);
        }


    }


}
