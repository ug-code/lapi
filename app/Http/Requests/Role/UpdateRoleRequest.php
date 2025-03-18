<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role,
            'guard_name' => 'sometimes|string|max:255'
        ];
    }
} 