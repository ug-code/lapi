<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapma yetkisi var mı?
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Yetkilendirme mantığınıza göre değiştirin
    }

    /**
     * İstek için doğrulama kuralları
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $this->route('id'),
            'description' => 'sometimes|nullable|string',
            // Diğer permission alanlarınızı buraya ekleyin
        ];
    }

    /**
     * Validation hatalarına özel mesajlar
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'Yetki adı zorunludur.',
            'name.unique' => 'Bu yetki adı zaten kullanılmaktadır.',
            'name.max' => 'Yetki adı en fazla 255 karakter olabilir.'
        ];
    }
}
