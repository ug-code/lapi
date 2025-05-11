<?php

namespace App\Services;

use App\Models\AiTool;
use App\Repositories\AiToolRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AiToolService
{
    /**
     * @var AiToolRepository
     */
    protected AiToolRepository $aiToolRepository;

    /**
     * AiToolService constructor
     *
     * @param AiToolRepository $aiToolRepository
     */
    public function __construct(AiToolRepository $aiToolRepository)
    {
        $this->aiToolRepository = $aiToolRepository;
    }

    /**
     * Tüm AI araçlarını sayfalama ile getirir
     *
     * @param int $perPage Sayfa başına gösterilecek kayıt sayısı
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllTools(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->aiToolRepository->getAllPaginated($perPage);
    }

    /**
     * ID'ye göre AI aracı getirir
     *
     * @param int $id
     * @return AiTool
     */
    public function getToolById(int $id): AiTool
    {
        return $this->aiToolRepository->findById($id);
    }

    /**
     * Yeni AI aracı oluşturur
     *
     * @param array $data
     * @return AiTool
     */
    public function createTool(array $data): AiTool
    {
        // Burada ek iş mantığı eklenebilir
        return $this->aiToolRepository->create($data);
    }

    /**
     * Mevcut AI aracını günceller
     *
     * @param int $id
     * @param array $data
     * @return AiTool
     */
    public function updateTool(int $id, array $data): AiTool
    {
        // Burada ek iş mantığı eklenebilir
        return $this->aiToolRepository->update($id, $data);
    }

    /**
     * AI aracını siler
     *
     * @param int $id
     * @return bool
     */
    public function deleteTool(int $id): bool
    {
        // Burada ek iş mantığı eklenebilir
        return $this->aiToolRepository->delete($id);
    }
}
