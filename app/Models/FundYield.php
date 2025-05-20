<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FundYield
 *
 * @property int $id
 * @property string $fund_id Fon kimlik bilgisi
 * @property string|null $query_params Sorgu parametreleri
 * @property array|null $response_data Yanıt verileri (JSON formatında)
 * @property \Illuminate\Support\Carbon|null $expires_at Cache'in geçerlilik süresi
 * @property \Illuminate\Support\Carbon|null $created_at Oluşturulma tarihi
 * @property \Illuminate\Support\Carbon|null $updated_at Güncellenme tarihi
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield query()
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereYieldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereQueryParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereResponseData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundYield whereUpdatedAt($value)
 */
class FundYield extends Model
{
    protected $fillable = [
        'fund_id',
        'query_params',
        'response_data',
        'expires_at'
    ];

    protected $casts = [

    ];
}
