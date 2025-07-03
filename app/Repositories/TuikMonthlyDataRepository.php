<?php

namespace App\Repositories;

use App\Models\TuikMonthlyData;

class TuikMonthlyDataRepository
{
    /**
     * Verilen yıl ve aya göre TÜFE değerini getirir.
     *
     * @param int $year
     * @param int $month
     * @return float|null
     */
    public function getValueByYearAndMonth(int $year, int $month): ?float
    {
        return TuikMonthlyData::where('year', $year)
            ->where('month', $month)
            ->value('value');
    }
}
