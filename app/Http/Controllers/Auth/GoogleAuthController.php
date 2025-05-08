<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\EloquentUserModel;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $user = EloquentUserModel::firstOrCreate(
                ['email' => $googleUser->email],
                [
                    'fullname' => $googleUser->name,
                    'password' => null,
                ]
            );

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => true,
                'message' => 'Google login successful',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Google login failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }
} 