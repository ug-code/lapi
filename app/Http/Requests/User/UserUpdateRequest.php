<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Gerekirse yetkilendirme koyarsın
    }

    public function rules(): array
    {
        return [
            'fullname' => 'string|max:255',
            'email'    => 'email|max:255|unique:users,email,' . $this->route('id'),
            'password' => 'nullable|string|min:6',
        ];
    }
}
