<?php

namespace App\Http\Requests\MobileApp;

use Illuminate\Foundation\Http\FormRequest;

class SetLearnKeywordRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'id'         => 'required|integer',
            'is_learned' => 'required|boolean',
        ];

    }
}
