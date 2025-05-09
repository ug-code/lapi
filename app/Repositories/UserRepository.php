<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class UserRepository
{
    /**
     * Kullanıcıları listeler
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return User::all();
    }

    /**
     * ID'ye göre kullanıcı getirir
     *
     * @param int $id
     * @return User
     */
    public function findById(int $id): User
    {
        return User::findOrFail($id);
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
        $user = $this->findById($userId);

        // Role ID ya da role adı olabilir
        if (is_numeric($roleId)) {
            $role = Role::findById($roleId);
            $user->assignRole($role);
        } else {
            $user->assignRole($roleId);
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
        $user = $this->findById($userId);
        return $user->roles;
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
        $user = $this->findById($userId);

        if (is_numeric($roleId)) {
            $role = Role::findById($roleId);
            $user->removeRole($role);
        } else {
            $user->removeRole($roleId);
        }

        return $user->fresh();
    }
}
