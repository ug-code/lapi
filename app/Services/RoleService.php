<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    /**
     * @var RoleRepository
     */
    protected RoleRepository $roleRepository;

    /**
     * RoleService constructor
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Yeni rol oluşturur
     *
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role
    {
        // Burada ek iş mantığı eklenebilir
        return $this->roleRepository->create($data);
    }

    /**
     * Rol siler
     *
     * @param int $roleId
     * @return bool
     */
    public function deleteRole(int $roleId): bool
    {
        // Burada ek iş mantığı eklenebilir
        return $this->roleRepository->delete($roleId);
    }

    /**
     * Rolleri listeler
     *
     * @return Collection
     */
    public function getRoles(): Collection
    {
        // Burada ek iş mantığı eklenebilir
        return $this->roleRepository->getAll();
    }

    /**
     * Rol detayını getirir
     *
     * @param int $roleId
     * @return Role
     */
    public function getRole(int $roleId): Role
    {
        // Burada ek iş mantığı eklenebilir
        return $this->roleRepository->findById($roleId);
    }

    /**
     * Rolü günceller
     *
     * @param int $roleId
     * @param array $data
     * @return Role
     */
    public function updateRole(int $roleId, array $data): Role
    {
        // Burada ek iş mantığı eklenebilir
        return $this->roleRepository->update($roleId, $data);
    }
}
