<?php

namespace App\Models;

use Illuminate\Support\Carbon;

/**
 * App\Models\FundYield
 *
 * @property int $id
 * @property string|null $fund_id Fon kimlik bilgisi
 * @property string|null $code Fon kodu
 * @property int|null $categories_id Yönetici şirket ID'si
 * @property string|null $management_company_id Yönetici şirket ID'si
 * @property string|null $title Fon başlığı
 * @property string|null $type Fon tipi
 * @property bool $tefas TEFAS'ta olup olmadığı
 * @property float|null $yield_1m 1 aylık nominal getiri
 * @property float|null $yield_3m 3 aylık nominal getiri
 * @property float|null $yield_6m 6 aylık nominal getiri
 * @property float|null $yield_ytd Yıl başından itibaren nominal getiri
 * @property float|null $yield_1y 1 yıllık nominal getiri
 * @property float|null $yield_3y 3 yıllık nominal getiri
 * @property float|null $yield_5y 5 yıllık nominal getiri
 * @property float|null $yield_1m_reel 1 aylık reel getiri
 * @property float|null $yield_3m_reel 3 aylık reel getiri
 * @property float|null $yield_6m_reel 6 aylık reel getiri
 * @property float|null $yield_ytd_reel Yıl başından itibaren reel getiri
 * @property float|null $yield_1y_reel 1 yıllık reel getiri
 * @property float|null $yield_3y_reel 3 yıllık reel getiri
 * @property float|null $yield_5y_reel 5 yıllık reel getiri
 * @property Carbon|null $expires_at Cache'in geçerlilik süresi
 * @property Carbon|null $created_at Oluşturulma tarihi
 * @property Carbon|null $updated_at Güncellenme tarihi
 */

class FundYield extends BaseModel
{
    protected $fillable = [
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
        'expires_at'
    ];

    protected $casts = [
        'tefas' => 'boolean',
        'yield_1m' => 'float',
        'yield_3m' => 'float',
        'yield_6m' => 'float',
        'yield_ytd' => 'float',
        'yield_1y' => 'float',
        'yield_3y' => 'float',
        'yield_5y' => 'float',
        'yield_1m_reel' => 'float',
        'yield_3m_reel' => 'float',
        'yield_6m_reel' => 'float',
        'yield_ytd_reel' => 'float',
        'yield_1y_reel' => 'float',
        'yield_3y_reel' => 'float',
        'yield_5y_reel' => 'float',
        'expires_at' => 'datetime'
    ];
}
