<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inflation\CalculateRequest;
use App\Models\TuikMonthlyData;

class InflationController extends Controller
{
    public function calculate(CalculateRequest $request)
    {

        $startYear = $request->input('start_year');
        $endYear = $request->input('end_year');
        $month = $request->input('month');
        $amount = $request->input('amount');

        $startTufe = TuikMonthlyData::where('year', $startYear)
            ->where('month', $month)
            ->value('value');

        $endTufe = TuikMonthlyData::where('year', $endYear)
            ->where('month', $month)
            ->value('value');

        if (!$startTufe || !$endTufe) {
            return response()->json([
                'error' => 'TÜFE verisi bulunamadı belirtilen yıllar için.'
            ], 404);
        }

        $changePercent = (($endTufe - $startTufe) / $startTufe) * 100;
        $newAmount = $amount * ($endTufe / $startTufe);
        $totalYears = $endYear - $startYear;

        return response()->json([
            'start_year' => $startYear,
            'end_year' => $endYear,
            'month' => $month,
            'original_amount' => round($amount, 2),
            'calculated_amount' => round($newAmount, 2),
            'start_tufe' => round($startTufe, 5),
            'end_tufe' => round($endTufe, 5),
            'total_years' => $totalYears,
            'total_change_percent' => round($changePercent, 2),
        ]);
    }
}
