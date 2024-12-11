<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int user_id
 * @property boolean is_learned
 * @property int category_id
 * @property string eng_keyword
 * @property string tr_keyword
 */
class AppKeyword extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $fillable
        = [
            'id',
            'user_id',
            'is_learned',
            'category_id',
            'eng_keyword',
            'tr_keyword',
        ];
}
