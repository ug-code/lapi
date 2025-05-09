<?php

namespace App\Repositories;

use App\Models\AppKeyword;
use Illuminate\Database\Eloquent\Collection;

class AppKeywordRepository
{
    /**
     * Anahtar kelimeleri listeler
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return AppKeyword::orderBy('eng_keyword')->get();
    }

    /**
     * Kullanıcının anahtar kelime sayısını getirir
     *
     * @param int $userId
     * @return int
     */
    public function countByUserId(int $userId): int
    {
        return AppKeyword::where('user_id', $userId)->count();
    }

    /**
     * ID'ye göre anahtar kelime getirir
     *
     * @param int $id
     * @return AppKeyword|null
     */
    public function findById(int $id): ?AppKeyword
    {
        return AppKeyword::find($id);
    }

    /**
     * Belirli bir kelimeyi kullanıcıya göre arar
     *
     * @param int $userId
     * @param string $engKeyword
     * @return AppKeyword|null
     */
    public function findByUserAndKeyword(int $userId, string $engKeyword): ?AppKeyword
    {
        return AppKeyword::where('user_id', $userId)
            ->where('eng_keyword', $engKeyword)
            ->first();
    }

    /**
     * Yeni anahtar kelime oluşturur
     *
     * @param array $data
     * @return AppKeyword
     */
    public function create(array $data): AppKeyword
    {
        $keyword = new AppKeyword();
        $keyword->user_id = $data['user_id'];
        $keyword->eng_keyword = $data['eng_keyword'] ?? null;
        $keyword->tr_keyword = $data['tr_keyword'] ?? null;
        $keyword->is_learned = $data['is_learned'] ?? false;
        $keyword->category_id = $data['category_id'] ?? 1;
        $keyword->detail = $data['detail'] ?? null;
        $keyword->save();

        return $keyword;
    }

    /**
     * Anahtar kelime öğrenilme durumunu günceller
     *
     * @param int $id
     * @param bool $isLearned
     * @return bool
     */
    public function updateLearnStatus(int $id, bool $isLearned): bool
    {
        return AppKeyword::find($id)->update(['is_learned' => $isLearned]);
    }
}
