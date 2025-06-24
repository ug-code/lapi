<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * UserService constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Kullanıcıları listeler
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * ID'ye göre kullanıcı getirir
     *
     * @param int $id
     * @return User
     */
    public function getUserById(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Kullanıcıya rol atar
     *
     * @param int $userId
     * @param int|string $roleId
     * @return User
     */
    public function assignRole(int $userId, $roleId): User
    {
        // Burada ek iş mantığı eklenebilir
        return $this->userRepository->assignRole($userId, $roleId);
    }

    /**
     * Kullanıcıya birden fazla rol atar
     *
     * @param int $userId
     * @param array $roleIds
     * @return User
     */
    public function assignMultipleRoles(int $userId, array $roleIds): User
    {
        $user = $this->userRepository->findById($userId);

        // Mevcut tüm rolleri kaldır
        $user->roles()->detach();

        foreach ($roleIds as $roleId) {
            // Eğer rol zaten atanmışsa atlayalım
            if (!$user->hasRole($roleId)) {
                $this->userRepository->assignRole($userId, $roleId);
            }
        }

        return $user->fresh();
    }

    /**
     * Kullanıcının rollerini getirir
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserRoles(int $userId): Collection
    {
        return $this->userRepository->getUserRoles($userId);
    }

    /**
     * Kullanıcıdan rol kaldırır
     *
     * @param int $userId
     * @param int|string $roleId
     * @return User
     */
    public function removeRole(int $userId, $roleId): User
    {
        // Burada ek iş mantığı eklenebilir
        return $this->userRepository->removeRole($userId, $roleId);
    }


    public function updateUser(int $userId, array $data)
    {
        $user = $this->userRepository->findById($userId);

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh();
    }
}
