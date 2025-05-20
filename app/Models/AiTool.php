<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AiTool
 *
 * @property int $id
 * @property string $name Yapay zeka aracının adı
 * @property string $api_key Yapay zeka aracına bağlanmak için API anahtarı
 * @property string $base_url Yapay zeka aracının temel URL'si
 * @property string|null $description Yapay zeka aracının açıklaması
 * @property bool $is_active Aracın aktif olup olmadığını belirten durum
 * @property \Illuminate\Support\Carbon|null $created_at Oluşturulma tarihi
 * @property \Illuminate\Support\Carbon|null $updated_at Güncellenme tarihi
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool query()
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereBaseUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiTool whereUpdatedAt($value)
 */
class AiTool extends Model
{
    protected $fillable = [
        'name',
        'api_key',
        'base_url',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean'
    ];
}
