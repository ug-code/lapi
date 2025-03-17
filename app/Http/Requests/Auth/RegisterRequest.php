<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'fullname' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
        ];

    }
}
