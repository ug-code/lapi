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
}
