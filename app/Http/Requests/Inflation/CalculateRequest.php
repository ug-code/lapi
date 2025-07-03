<?php

namespace App\Http\Requests\Inflation;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_year' => 'required|integer',
            'start_month' => 'required|integer|between:1,12',
            'end_year' => 'required|integer|gte:start_year',
            'end_month' => 'required|integer|between:1,12',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'start_year.required' => 'Başlangıç yılı gerekli.',
            'start_month.required' => 'Başlangıç ayı gerekli.',
            'start_month.between' => 'Başlangıç ayı 1 ile 12 arasında olmalıdır.',
            'end_year.required' => 'Bitiş yılı gerekli.',
            'end_year.gte' => 'Bitiş yılı başlangıç yılından küçük olamaz.',
            'end_month.required' => 'Bitiş ayı gerekli.',
            'end_month.between' => 'Bitiş ayı 1 ile 12 arasında olmalıdır.',
            'amount.min' => 'Tutar negatif olamaz.',
        ];
    }
}
