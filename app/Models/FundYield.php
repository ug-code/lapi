<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundYield extends Model
{
    protected $fillable = [
        'fund_id',
        'yield_value',
        'date',
        'raw_data'
    ];

    protected $casts = [
        'raw_data' => 'json',
        'date' => 'datetime'
    ];
} 