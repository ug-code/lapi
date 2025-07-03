<?php

namespace App\Http\Requests\Finance;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FundYieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Yetkilendirme gerekiyorsa burası değiştirilebilir
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
            'filter' => 'nullable|array',
            'filter.code' => 'nullable|string|max:50',
            'filter.categories_id' => 'nullable|array',
            'filter.categories_id.*' => 'integer',
            'filter.management_company_id' => 'nullable|array',
            'filter.management_company_id.*' => 'string|max:50',
            'filter.title' => 'nullable|string|max:255',
            'filter.type' => 'nullable|string|max:100',
            'filter.tefas' => 'nullable|boolean',

            'filter.yield_1m' => 'nullable|array',
            'filter.yield_1m.min' => 'nullable|numeric',
            'filter.yield_1m.max' => 'nullable|numeric|gte:filter.yield_1m.min',

            'filter.yield_3m' => 'nullable|array',
            'filter.yield_3m.min' => 'nullable|numeric',
            'filter.yield_3m.max' => 'nullable|numeric|gte:filter.yield_3m.min',

            'filter.yield_6m' => 'nullable|array',
            'filter.yield_6m.min' => 'nullable|numeric',
            'filter.yield_6m.max' => 'nullable|numeric|gte:filter.yield_6m.min',

            'filter.yield_ytd' => 'nullable|array',
            'filter.yield_ytd.min' => 'nullable|numeric',
            'filter.yield_ytd.max' => 'nullable|numeric|gte:filter.yield_ytd.min',

            'filter.yield_1y' => 'nullable|array',
            'filter.yield_1y.min' => 'nullable|numeric',
            'filter.yield_1y.max' => 'nullable|numeric|gte:filter.yield_1y.min',

            'filter.yield_3y' => 'nullable|array',
            'filter.yield_3y.min' => 'nullable|numeric',
            'filter.yield_3y.max' => 'nullable|numeric|gte:filter.yield_3y.min',

            'filter.yield_5y' => 'nullable|array',
            'filter.yield_5y.min' => 'nullable|numeric',
            'filter.yield_5y.max' => 'nullable|numeric|gte:filter.yield_5y.min',

            'filter.yield_1m_reel' => 'nullable|array',
            'filter.yield_1m_reel.min' => 'nullable|numeric',
            'filter.yield_1m_reel.max' => 'nullable|numeric|gte:filter.yield_1m_reel.min',

            'filter.yield_3m_reel' => 'nullable|array',
            'filter.yield_3m_reel.min' => 'nullable|numeric',
            'filter.yield_3m_reel.max' => 'nullable|numeric|gte:filter.yield_3m_reel.min',

            'filter.yield_6m_reel' => 'nullable|array',
            'filter.yield_6m_reel.min' => 'nullable|numeric',
            'filter.yield_6m_reel.max' => 'nullable|numeric|gte:filter.yield_6m_reel.min',

            'filter.yield_ytd_reel' => 'nullable|array',
            'filter.yield_ytd_reel.min' => 'nullable|numeric',
            'filter.yield_ytd_reel.max' => 'nullable|numeric|gte:filter.yield_ytd_reel.min',

            'filter.yield_1y_reel' => 'nullable|array',
            'filter.yield_1y_reel.min' => 'nullable|numeric',
            'filter.yield_1y_reel.max' => 'nullable|numeric|gte:filter.yield_1y_reel.min',

            'filter.yield_3y_reel' => 'nullable|array',
            'filter.yield_3y_reel.min' => 'nullable|numeric',
            'filter.yield_3y_reel.max' => 'nullable|numeric|gte:filter.yield_3y_reel.min',

            'filter.yield_5y_reel' => 'nullable|array',
            'filter.yield_5y_reel.min' => 'nullable|numeric',
            'filter.yield_5y_reel.max' => 'nullable|numeric|gte:filter.yield_5y_reel.min',

            'sort' => [
                'nullable',
                'string',
                Rule::in([
                    'code',
                    'categories_id',
                    'management_company_id',
                    'title',
                    'type',
                    'tefas',
                    'yield_1m',
                    'yield_3m',
                    'yield_6m',
                    'yield_ytd',
                    'yield_1y',
                    'yield_3y',
                    'yield_5y',
                    'yield_1m_reel',
                    'yield_3m_reel',
                    'yield_6m_reel',
                    'yield_ytd_reel',
                    'yield_1y_reel',
                    'yield_3y_reel',
                    'yield_5y_reel',
                    'created_at',
                    'updated_at'
                ])
            ],
            'direction' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc'])
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'search' => 'Arama metni',
            'filter' => 'Filtreler',
            'filter.code' => 'Fon kodu',
            'filter.categories_id' => 'Kategori ID',
            'filter.categories_id.*' => 'Kategori ID değeri',
            'filter.management_company_id' => 'Yönetici şirket ID',
            'filter.title' => 'Fon başlığı',
            'filter.type' => 'Fon tipi',
            'filter.tefas' => 'TEFAS durumu',

            'filter.yield_1m' => '1 aylık getiri',
            'filter.yield_1m.min' => '1 aylık minimum getiri',
            'filter.yield_1m.max' => '1 aylık maksimum getiri',

            'filter.yield_3m' => '3 aylık getiri',
            'filter.yield_3m.min' => '3 aylık minimum getiri',
            'filter.yield_3m.max' => '3 aylık maksimum getiri',

            'filter.yield_6m' => '6 aylık getiri',
            'filter.yield_6m.min' => '6 aylık minimum getiri',
            'filter.yield_6m.max' => '6 aylık maksimum getiri',

            'filter.yield_ytd' => 'Yıl başından itibaren getiri',
            'filter.yield_ytd.min' => 'Yıl başından itibaren minimum getiri',
            'filter.yield_ytd.max' => 'Yıl başından itibaren maksimum getiri',

            'filter.yield_1y' => '1 yıllık getiri',
            'filter.yield_1y.min' => '1 yıllık minimum getiri',
            'filter.yield_1y.max' => '1 yıllık maksimum getiri',

            'filter.yield_3y' => '3 yıllık getiri',
            'filter.yield_3y.min' => '3 yıllık minimum getiri',
            'filter.yield_3y.max' => '3 yıllık maksimum getiri',

            'filter.yield_5y' => '5 yıllık getiri',
            'filter.yield_5y.min' => '5 yıllık minimum getiri',
            'filter.yield_5y.max' => '5 yıllık maksimum getiri',

            'filter.yield_1m_reel' => '1 aylık reel getiri',
            'filter.yield_1m_reel.min' => '1 aylık minimum reel getiri',
            'filter.yield_1m_reel.max' => '1 aylık maksimum reel getiri',

            'filter.yield_3m_reel' => '3 aylık reel getiri',
            'filter.yield_3m_reel.min' => '3 aylık minimum reel getiri',
            'filter.yield_3m_reel.max' => '3 aylık maksimum reel getiri',

            'filter.yield_6m_reel' => '6 aylık reel getiri',
            'filter.yield_ytd_reel' => 'Yıl başından itibaren reel getiri',
            'filter.yield_ytd_reel.min' => 'Yıl başından itibaren minimum reel getiri',
            'filter.yield_ytd_reel.max' => 'Yıl başından itibaren maksimum reel getiri',
    
            'filter.yield_1y_reel' => '1 yıllık reel getiri',
            'filter.yield_1y_reel.min' => '1 yıllık minimum reel getiri',
            'filter.yield_1y_reel.max' => '1 yıllık maksimum reel getiri',
    
            'filter.yield_3y_reel' => '3 yıllık reel getiri',
            'filter.yield_3y_reel.min' => '3 yıllık minimum reel getiri',
            'filter.yield_3y_reel.max' => '3 yıllık maksimum reel getiri',
    
            'filter.yield_5y_reel' => '5 yıllık reel getiri',
            'filter.yield_5y_reel.min' => '5 yıllık minimum reel getiri',
            'filter.yield_5y_reel.max' => '5 yıllık maksimum reel getiri',
    
            'sort' => 'Sıralama alanı',
            'direction' => 'Sıralama yönü',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'filter.*.min.numeric' => ':attribute sayısal bir değer olmalıdır.',
            'filter.*.max.numeric' => ':attribute sayısal bir değer olmalıdır.',
            'filter.*.max.gte' => ':attribute, minimum değerden büyük veya eşit olmalıdır.',
            'sort.in' => 'Geçersiz sıralama alanı. Lütfen geçerli bir alan seçin.',
            'direction.in' => 'Sıralama yönü "asc" veya "desc" olmalıdır.',
        ];
    }
}
    
