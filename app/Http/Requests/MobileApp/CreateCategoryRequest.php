<?php

namespace App\Http\Requests\MobileApp;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'description' => 'required|string',
        ];

    }
}
