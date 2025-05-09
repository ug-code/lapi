<?php

namespace App\Repositories;

use App\Models\AppKeywordCategory;
use Illuminate\Database\Eloquent\Collection;

class AppKeywordCategoryRepository
{
    /**
     * Kullanıcının kategorilerini getirir
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return AppKeywordCategory::where("user_id", $userId)->get();
    }

    /**
     * Kategori oluşturur veya varsa getirir
     *
     * @param string $description
     * @param int $userId
     * @return AppKeywordCategory
     */
    public function firstOrCreate(string $description, int $userId): AppKeywordCategory
    {
        return AppKeywordCategory::firstOrCreate(
            ['description' => $description],
            ['user_id' => $userId]
        );
    }
}
