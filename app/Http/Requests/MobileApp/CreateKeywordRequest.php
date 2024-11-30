<?php

namespace App\Http\Requests\MobileApp;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateKeywordRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id'     => 'required|string',
            'eng_keyword' => 'required|string',
            'tr_keyword'  => 'required|string',
            'category'    => 'string',
        ];

    }
}
