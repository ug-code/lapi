<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthService
{
    /**
     * Kullanıcı kaydı oluşturur
     */
    public function register(array $data): User
    {
        $user = new User();
        $user->fullname = $data['fullname'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->save();

        return $user;
    }

    /**
     * Kullanıcı girişi yapar
     */
    public function login(array $credentials): ?string
    {
        return auth()->attempt($credentials);
    }

    /**
     * Kullanıcı listesini getirir
     */
    public function getUserList(int $perPage = 10)
    {
        return User::with('roles:id,name')->paginate($perPage);
    }

    /**
     * Mevcut kullanıcıyı getirir
     */
    public function getAuthenticatedUser()
    {
        return auth()->user();
    }

    /**
     * Kullanıcı çıkışı yapar
     */
    public function logout(): void
    {
        auth()->logout();
    }

    /**
     * Token'ı yeniler
     */
    public function refreshToken(): string
    {
        return auth()->refresh();
    }

    /**
     * Token yanıt yapısını oluşturur
     */
    public function createTokenResponse(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
