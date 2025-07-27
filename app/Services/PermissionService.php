<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function getAll()
    {
        return Permission::all();
    }

    public function create(array $data)
    {
        return Permission::create([
            'name' => $data['name']
        ]);
    }

    /**
     * Var olan bir permission'ı günceller
     * 
     * @param int $id
     * @param array $data
     * @return Permission
     */
    public function update(int $id, array $data)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data);

        return $permission->fresh();
    }
} 