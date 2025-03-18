<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    /**
     * Yeni rol oluşturur
     */
    public function createRole(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'api'
        ]);
    }

    /**
     * Rol siler
     */
    public function deleteRole(int $roleId): bool
    {
        $role = Role::findOrFail($roleId);
        return $role->delete();
    }

    /**
     * Rolleri listeler
     */
    public function getRoles(int $perPage = 10): LengthAwarePaginator
    {
        return Role::paginate($perPage);
    }

    /**
     * Rol detayını getirir
     */
    public function getRole(int $roleId): Role
    {
        return Role::findOrFail($roleId);
    }

    /**
     * Rolü günceller
     */
    public function updateRole(int $roleId, array $data): Role
    {
        $role = Role::findOrFail($roleId);
        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $role->guard_name
        ]);
        
        return $role;
    }
} 