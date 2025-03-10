<?php

namespace App\Http\Requests\AIChat;

use Illuminate\Foundation\Http\FormRequest;

class ChatWithAIRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'message' => 'required|string',
        ];

    }
}
