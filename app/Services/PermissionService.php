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
} 