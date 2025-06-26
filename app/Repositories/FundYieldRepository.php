<?php

namespace App\Repositories;

use App\Models\FundYield;

class FundYieldRepository
{
    public function getFundYieldFromCache(
        ?string $search = null,
        array   $filter = [],
        ?string $sort = null,
        ?string $sortDirection = 'asc'
    ): ?array {
        $hasCacheRecords = FundYield::where('expires_at', '>', now())->exists();
        if (!$hasCacheRecords) {
            return null;
        }
        $query = FundYield::where('expires_at', '>', now());
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                    ->orWhere('title', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%")
                    ->orWhere('management_company_id', 'like', "%$search%")
                    ->orWhere('categories_id', 'like', "%$search%");
            });
        }
        if (!empty($filter)) {
            foreach ($filter as $field => $value) {
                if (str_starts_with($field, 'yield_') && is_array($value)) {
                    if (isset($value['min'])) {
                        $query->where($field, '>=', $value['min']);
                    }
                    if (isset($value['max'])) {
                        $query->where($field, '<=', $value['max']);
                    }
                } elseif ($field === 'management_company_id' && is_array($value)) {
                    $query->whereIn($field, $value);
                } elseif ($field === 'categories_id' && is_array($value)) {
                    $query->whereIn($field, $value);
                } elseif ($field === 'tefas') {
                    $boolValue = $value ? 'true' : 'false';
                    $query->where($field, $boolValue);
                } else {
                    $query->where($field, $value);
                }
            }
        }
        if ($sort) {
            $direction = $sortDirection && strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $cacheRecords = $query->get()->toArray();
        return [
            "start"   => null,
            "end"     => null,
            "results" => $cacheRecords
        ];
    }

    public function cacheFundYieldResult(array $result): void
    {
        try {
            $results = $result['results'] ?? null;
            if (isset($results) && is_array($results) && !empty($results)) {
                $data = [];
                foreach ($results as $fund) {
                    $managementCompanyId = $fund['management_company_id'] ?? null;
                    $categoriesId = $fund['categories_id'] ?? $fund['categories__id'] ?? null;
                    if (is_array($managementCompanyId)) {
                        $managementCompanyId = !empty($managementCompanyId) ? $managementCompanyId[0] : null;
                    }
                    if (is_array($categoriesId)) {
                        $categoriesId = !empty($categoriesId) ? $categoriesId[0] : null;
                    }
                    $data[] = [
                        'code'                  => $fund['code'] ?? null,
                        'categories_id'         => $categoriesId,
                        'management_company_id' => $managementCompanyId,
                        'title'                 => $fund['title'] ?? null,
                        'type'                  => $fund['type'] ?? null,
                        'tefas'                 => $fund['tefas'] ? 'true' : 'false',
                        'yield_1m'              => $fund['yield_1m'] ?? 0,
                        'yield_3m'              => $fund['yield_3m'] ?? 0,
                        'yield_6m'              => $fund['yield_6m'] ?? 0,
                        'yield_ytd'             => $fund['yield_ytd'] ?? 0,
                        'yield_1y'              => $fund['yield_1y'] ?? 0,
                        'yield_3y'              => $fund['yield_3y'] ?? 0,
                        'yield_5y'              => $fund['yield_5y'] ?? 0,
                        'expires_at'            => now()->addDay(),
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ];
                }
                FundYield::insert($data);
            } else {
                \Log::warning('Fon getirisi verisi beklenen formatta değil', [
                    'result' => $result
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Cache kaydetme hatası: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
} 