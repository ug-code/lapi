<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiTool extends Model
{
    protected $fillable = [
        'name',
        'api_key',
        'base_url',
        'description',
        'is_active',
    ];
}
