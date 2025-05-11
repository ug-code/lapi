<?php

namespace App\Repositories;

use App\Models\AiTool;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AiToolRepository
{
    /**
     * Tüm AI araçlarını getirir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return AiTool::all();
    }

    /**
     * Tüm AI araçlarını sayfalama ile getirir
     *
     * @param int $perPage Sayfa başına gösterilecek kayıt sayısı
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return AiTool::paginate($perPage);
    }

    /**
     * ID'ye göre AI aracı getirir
     *
     * @param int $id
     * @return AiTool
     */
    public function findById(int $id): AiTool
    {
        return AiTool::findOrFail($id);
    }

    /**
     * Yeni AI aracı oluşturur
     *
     * @param array $data
     * @return AiTool
     */
    public function create(array $data): AiTool
    {
        return AiTool::create($data);
    }

    /**
     * Mevcut AI aracını günceller
     *
     * @param int $id
     * @param array $data
     * @return AiTool
     */
    public function update(int $id, array $data): AiTool
    {
        $tool = $this->findById($id);
        $tool->update($data);

        return $tool;
    }

    /**
     * AI aracını siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $tool = $this->findById($id);
        return $tool->delete();
    }
}
