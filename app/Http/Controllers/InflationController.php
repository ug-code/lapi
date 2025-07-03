<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inflation\CalculateRequest;
use App\Models\TuikMonthlyData;

class InflationController extends Controller
{
    public function calculate(CalculateRequest $request)
    {

        $startYear = $request->input('start_year');
        $startMonth = $request->input('start_month');
        $endYear = $request->input('end_year');
        $endMonth = $request->input('end_month');
        $amount = $request->input('amount');

        // Başlangıç TÜFE değeri
        $startTufe = TuikMonthlyData::where('year', $startYear)
            ->where('month', $startMonth)
            ->value('value');

        // Bitiş TÜFE değeri
        $endTufe = TuikMonthlyData::where('year', $endYear)
            ->where('month', $endMonth)
            ->value('value');

        if (!$startTufe || !$endTufe) {
            return response()->json([
                'error' => 'Belirtilen tarihler için TÜFE verisi bulunamadı.'
            ], 404);
        }

        // Yıllar arasındaki fark tam yıl olmayabilir, ay farkını da hesaplayabiliriz
        $yearDiff = $endYear - $startYear;
        $monthDiff = $endMonth - $startMonth;
        $totalMonths = $yearDiff * 12 + $monthDiff;
        $totalYears = $totalMonths / 12;

        // Değişim yüzdesi
        $changePercent = (($endTufe - $startTufe) / $startTufe) * 100;

        // Yeni tutar
        $newAmount = $amount * ($endTufe / $startTufe);

        return response()->json([
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
        ]);

    }
}
