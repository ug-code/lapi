<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionsToRoleRequest extends FormRequest
{
    public function authorize()
    {
        // Gerekirse burada yetki kontrolü yapılabilir
        return true;
    }

    public function rules()
    {
        return [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ];
    }
} 