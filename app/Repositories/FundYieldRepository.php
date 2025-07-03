<?php

namespace App\Repositories;

use App\Models\FundYield;
use App\Models\TuikMonthlyData;
use Illuminate\Support\Carbon;

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

                // Şimdiki tarih (örn: 2025-07-01) olarak varsayıyorum, dinamik olabilir
                $currentDate = Carbon::now()->startOfMonth();

                // TuikMonthlyData verilerini bulk çek
                $tuikData = $this->getTuikDataBulk($currentDate);

                foreach ($results as $fund) {
                    $managementCompanyId = $fund['management_company_id'] ?? null;
                    $categoriesId = $fund['categories_id'] ?? $fund['categories__id'] ?? null;
                    if (is_array($managementCompanyId)) {
                        $managementCompanyId = !empty($managementCompanyId) ? $managementCompanyId[0] : null;
                    }
                    if (is_array($categoriesId)) {
                        $categoriesId = !empty($categoriesId) ? $categoriesId[0] : null;
                    }

                    // Nominal değerler, oran olarak (örn: %5 ise 0.05 olarak)
                    $nominal_1m = $fund['yield_1m'] ?? 0;
                    $nominal_3m = $fund['yield_3m'] ?? 0;
                    $nominal_6m = $fund['yield_6m'] ?? 0;
                    $nominal_ytd = $fund['yield_ytd'] ?? 0;
                    $nominal_1y = $fund['yield_1y'] ?? 0;
                    $nominal_3y = $fund['yield_3y'] ?? 0;
                    $nominal_5y = $fund['yield_5y'] ?? 0;

                    // TÜFE değerlerini çekmek için yardımcı fonksiyon
                    $getInflationRate = function (Carbon $start, Carbon $end) use ($tuikData): ?float {
                        $startKey = $start->year . '-' . $start->month;
                        $endKey = $end->year . '-' . ($end->month - 1);

                        $startData = $tuikData[$startKey] ?? null;
                        $endData = $tuikData[$endKey] ?? null;

                        if (!$startData || !$endData) return null;

                        return ($endData / $startData) - 1;
                    };

                    // Tarihleri hesapla
                    $date_1m_ago = $currentDate->copy()->subMonth(1);
                    $date_3m_ago = $currentDate->copy()->subMonths(3);
                    $date_6m_ago = $currentDate->copy()->subMonths(6);
                    $date_ytd_start = Carbon::create($currentDate->year, 1, 1); // yıl başı
                    $date_1y_ago = $currentDate->copy()->subYear(1);
                    $date_3y_ago = $currentDate->copy()->subYears(3);
                    $date_5y_ago = $currentDate->copy()->subYears(5);

                    // Enflasyon oranları
                    $infl_1m = $getInflationRate($date_1m_ago, $currentDate);
                    $infl_3m = $getInflationRate($date_3m_ago, $currentDate);
                    $infl_6m = $getInflationRate($date_6m_ago, $currentDate);
                    $infl_ytd = $getInflationRate($date_ytd_start, $currentDate);
                    $infl_1y = $getInflationRate($date_1y_ago, $currentDate);
                    $infl_3y = $getInflationRate($date_3y_ago, $currentDate);
                    $infl_5y = $getInflationRate($date_5y_ago, $currentDate);

                    // Reel getiriyi hesapla, nominal oranlar 0.05 gibi varsayılıyor.
                    $calcReel = function ($nominal, $inflation) {
                        if ($inflation === null) return null;
                        return (($nominal + 1) / ($inflation + 1)) - 1;
                    };

                    $data[] = [
                        'code'                  => $fund['code'] ?? null,
                        'categories_id'         => $categoriesId,
                        'management_company_id' => $managementCompanyId,
                        'title'                 => $fund['title'] ?? null,
                        'type'                  => $fund['type'] ?? null,
                        'tefas'                 => $fund['tefas'] ? 'true' : 'false',
                        'yield_1m'              => $nominal_1m,
                        'yield_3m'              => $nominal_3m,
                        'yield_6m'              => $nominal_6m,
                        'yield_ytd'             => $nominal_ytd,
                        'yield_1y'              => $nominal_1y,
                        'yield_3y'              => $nominal_3y,
                        'yield_5y'              => $nominal_5y,

                        // reel değerler yüzde değil, oran şeklinde (0.03 = %3)
                        'yield_1m_reel'         => $calcReel($nominal_1m, $infl_1m),
                        'yield_3m_reel'         => $calcReel($nominal_3m, $infl_3m),
                        'yield_6m_reel'         => $calcReel($nominal_6m, $infl_6m),
                        'yield_ytd_reel'        => $calcReel($nominal_ytd, $infl_ytd),
                        'yield_1y_reel'         => $calcReel($nominal_1y, $infl_1y),
                        'yield_3y_reel'         => $calcReel($nominal_3y, $infl_3y),
                        'yield_5y_reel'         => $calcReel($nominal_5y, $infl_5y),

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

    private function getTuikDataBulk(Carbon $currentDate): array
    {
        $startDate = $currentDate->copy()->subYears(5);

        $tuikRecords = TuikMonthlyData::where('year', '>=', $startDate->year)
            ->where('year', '<=', $currentDate->year)
            ->get(['year', 'month', 'value']);

        $tuikData = [];
        foreach ($tuikRecords as $record) {
            $key = $record->year . '-' . $record->month;
            $tuikData[$key] = $record->value;
        }

        return $tuikData;
    }
}
