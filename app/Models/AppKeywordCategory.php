<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int id
 * @property int user_id
 * @property boolean description
 */
class AppKeywordCategory extends BaseModel
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
