<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    /**
     * Tüm rolleri getirir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Role::all();
    }

    /**
     * ID'ye göre rol getirir
     *
     * @param int $id
     * @return Role
     */
    public function findById(int $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Yeni rol oluşturur
     *
     * @param array $data
     * @return Role
     */
    public function create(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'api'
        ]);
    }

    /**
     * Mevcut rolü günceller
     *
     * @param int $id
     * @param array $data
     * @return Role
     */
    public function update(int $id, array $data): Role
    {
        $role = $this->findById($id);
        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $role->guard_name
        ]);

        return $role;
    }

    /**
     * Rol siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $role = $this->findById($id);
        return $role->delete();
    }
}
