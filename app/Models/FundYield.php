<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FundYield
 *
 * @property int $id
 * @property string|null $fund_id Fon kimlik bilgisi
 * @property string|null $code Fon kodu
 * @property string|null $management_company_id Yönetici şirket ID'si
 * @property string|null $title Fon başlığı
 * @property string|null $type Fon tipi
 * @property bool $tefas TEFAS'ta olup olmadığı
 * @property float|null $yield_1m 1 aylık getiri
 * @property float|null $yield_3m 3 aylık getiri
 * @property float|null $yield_6m 6 aylık getiri
 * @property float|null $yield_ytd Yıl başından itibaren getiri
 * @property float|null $yield_1y 1 yıllık getiri
 * @property float|null $yield_3y 3 yıllık getiri
 * @property float|null $yield_5y 5 yıllık getiri
 * @property \Illuminate\Support\Carbon|null $expires_at Cache'in geçerlilik süresi
 * @property \Illuminate\Support\Carbon|null $created_at Oluşturulma tarihi
 * @property \Illuminate\Support\Carbon|null $updated_at Güncellenme tarihi
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield query()
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereManagementCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereTefas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYield1m($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYield3m($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYield6m($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYieldYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYield1y($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYield3y($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYield5y($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereQueryParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereResponseData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereUpdatedAt($value)
 */
class FundYield extends Model
{
    protected $fillable = [

        'code',
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
        'expires_at' => 'datetime'
    ];
}
