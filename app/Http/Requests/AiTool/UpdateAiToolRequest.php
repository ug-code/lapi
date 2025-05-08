<?php

namespace App\Http\Requests\AiTool;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAiToolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Yetkilendirme mantığınıza göre true veya false döndürebilirsiniz
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'api_key' => 'sometimes|required|string|max:255',
            'base_url' => 'sometimes|required|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
