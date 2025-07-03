<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuikMonthlyData extends Model
{
    protected $table = 'tuik_monthly_data';

    protected $fillable = [
        'year',
        'month',
        'value',
    ];

    public $timestamps = false; // çünkü tabloda created_at, updated_at yok
}
