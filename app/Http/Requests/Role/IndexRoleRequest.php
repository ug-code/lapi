<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class IndexRoleRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100'
        ];
    }

    public function getPerPage(): int
    {
        return $this->get('per_page', 10);
    }
} 