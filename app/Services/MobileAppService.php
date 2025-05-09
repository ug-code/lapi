<?php

namespace App\Services;

use App\Models\AppKeyword;
use App\Models\AppKeywordCategory;
use App\Repositories\AppKeywordCategoryRepository;
use App\Repositories\AppKeywordRepository;
use Illuminate\Database\Eloquent\Collection;

class MobileAppService
{
    /**
     * @var AppKeywordRepository
     */
    protected AppKeywordRepository $keywordRepository;

    /**
     * @var AppKeywordCategoryRepository
     */
    protected AppKeywordCategoryRepository $categoryRepository;

    /**
     * @var DictionaryService
     */
    protected DictionaryService $dictionaryService;

    /**
     * MobileAppService constructor
     *
     * @param AppKeywordRepository $keywordRepository
     * @param AppKeywordCategoryRepository $categoryRepository
     * @param DictionaryService $dictionaryService
     */
    public function __construct(
        AppKeywordRepository $keywordRepository,
        AppKeywordCategoryRepository $categoryRepository,
        DictionaryService $dictionaryService
    ) {
        $this->keywordRepository = $keywordRepository;
        $this->categoryRepository = $categoryRepository;
        $this->dictionaryService = $dictionaryService;
    }

    /**
     * Kelime listesini getirir
     *
     * @return Collection
     */
    public function getKeywordList(): Collection
    {
        return $this->keywordRepository->getAll();
    }

    /**
     * Kelime sayısını getirir
     *
     * @param int $userId
     * @return int
     */
    public function getKeywordCount(int $userId): int
    {
        return $this->keywordRepository->countByUserId($userId);
    }

    /**
     * ID'ye göre kelime detayını ve çevirisini getirir
     *
     * @param int $id
     * @return array
     */
    public function getKeywordWithTranslation(int $id): array
    {
        $keyword = $this->keywordRepository->findById($id);
        $translate = $this->dictionaryService->scan($keyword->eng_keyword);

        return [
            'keyword' => $keyword,
            'translate' => $translate
        ];
    }

    /**
     * Kelime oluşturur
     *
     * @param array $data
     * @return array Oluşturma sonucunu ve kelimeyi içeren dizi
     */
    public function createKeyword(array $data): array
    {
        // Aynı kelimeyi tekrar eklemeyi engelle
        $existingKeyword = $this->keywordRepository->findByUserAndKeyword(
            $data['user_id'],
            $data['eng_keyword']
        );

        if ($existingKeyword) {
            return [
                'success' => false,
                'message' => 'Kelime mevcut.',
                'data' => $existingKeyword
            ];
        }

        // Dictionary servisinden kelime anlamını getir
        $detail = $this->dictionaryService->scan($data['eng_keyword']);
        $data['detail'] = $detail;

        // Kelimeyi oluştur
        $keyword = $this->keywordRepository->create($data);

        return [
            'success' => true,
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data' => $keyword
        ];
    }

    /**
     * Kelime öğrenilme durumunu günceller
     *
     * @param int $id
     * @param bool $isLearned
     * @return bool
     */
    public function setLearnKeyword(int $id, bool $isLearned): bool
    {
        return $this->keywordRepository->updateLearnStatus($id, $isLearned);
    }

    /**
     * Kelime çevirisi yapar
     *
     * @param string $keyword
     * @return array
     */
    public function translate(string $keyword): array
    {
        return $this->dictionaryService->scan($keyword);
    }

    /**
     * Kategori oluşturur
     *
     * @param string $description
     * @param int $userId
     * @return array Oluşturma sonucunu ve kategoriyi içeren dizi
     */
    public function createCategory(string $description, int $userId): array
    {
        $category = $this->categoryRepository->firstOrCreate($description, $userId);
        $message = $category->wasRecentlyCreated ? 'Başarılı bir şekilde kaydedildi.' : 'Kayıt zaten mevcut';

        return [
            'success' => true,
            'message' => $message,
            'data' => $category
        ];
    }

    /**
     * Kullanıcının kategorilerini getirir
     *
     * @param int $userId
     * @return Collection
     */
    public function getCategories(int $userId): Collection
    {
        return $this->categoryRepository->getByUserId($userId);
    }
}
