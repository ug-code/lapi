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
 */
class AiTool extends BaseModel
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
