<?php

namespace App\Services;

use App\Models\AiTool;
use App\Repositories\AiToolRepository;
use Illuminate\Database\Eloquent\Collection;

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
     * Tüm AI araçlarını getirir
     *
     * @return Collection
     */
    public function getAllTools(): Collection
    {
        return $this->aiToolRepository->getAll();
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
