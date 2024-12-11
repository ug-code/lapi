<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppKeywordCategory extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $fillable
                          = [
            'id',
            'user_id',
            'description',
        ];
}
