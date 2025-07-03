<?php

namespace App\Services;

use App\Repositories\TuikMonthlyDataRepository;

class InflationService
{
    protected TuikMonthlyDataRepository $repository;

    public function __construct(TuikMonthlyDataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * TÜFE hesaplaması yapar.
     *
     * @param int $startYear
     * @param int $startMonth
     * @param int $endYear
     * @param int $endMonth
     * @param float $amount
     * @return array|null
     */
    public function calculate(
        int $startYear,
        int $startMonth,
        int $endYear,
        int $endMonth,
        float $amount
    ): ?array {
        $startTufe = $this->repository->getValueByYearAndMonth($startYear, $startMonth);
        $endTufe = $this->repository->getValueByYearAndMonth($endYear, $endMonth);

        if (!$startTufe || !$endTufe) {
            return null;
        }

        $yearDiff = $endYear - $startYear;
        $monthDiff = $endMonth - $startMonth;
        $totalMonths = $yearDiff * 12 + $monthDiff;
        $totalYears = $totalMonths / 12;

        $changePercent = (($endTufe - $startTufe) / $startTufe) * 100;
        $newAmount = $amount * ($endTufe / $startTufe);

        return [
            'start_year' => $startYear,
            'start_month' => $startMonth,
            'end_year' => $endYear,
            'end_month' => $endMonth,
            'original_amount' => round($amount, 2),
            'calculated_amount' => round($newAmount, 2),
            'start_tufe' => round($startTufe, 5),
            'end_tufe' => round($endTufe, 5),
            'total_months' => $totalMonths,
            'total_years' => round($totalYears, 2),
            'total_change_percent' => round($changePercent, 2),
        ];
    }
}
