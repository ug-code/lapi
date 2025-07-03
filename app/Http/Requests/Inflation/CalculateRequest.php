<?php

namespace App\Http\Requests\Inflation;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // İzin veriyoruz, istersen burayı şartlara göre özelleştirebilirsin.
    }

    public function rules()
    {
        return [
            'start_year' => 'required|integer',
            'end_year' => 'required|integer|gte:start_year',
            'month' => 'required|integer|between:1,12',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'start_year.required' => 'Başlangıç yılı gerekli.',
            'end_year.required' => 'Bitiş yılı gerekli.',
            'end_year.gte' => 'Bitiş yılı başlangıç yılından küçük olamaz.',
            'month.between' => 'Ay 1 ile 12 arasında olmalıdır.',
            'amount.min' => 'Tutar negatif olamaz.',
        ];
    }
}
