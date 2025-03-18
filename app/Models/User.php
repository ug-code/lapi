<?php

namespace App\Models;

use App\Models\User\EloquentUserModel;
use App\Models\User\MongoUserModel;

// Aktif veritabanını kontrol et
if (env('USE_MONGO_USER_MODEL')) {
    class User extends MongoUserModel {}
} else {
    class User extends EloquentUserModel {}
}
