<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inflation\CalculateRequest;
use App\Services\InflationService;

class InflationController extends Controller
{
    protected InflationService $inflationService;

    public function __construct(InflationService $inflationService)
    {
        $this->inflationService = $inflationService;
    }

    public function calculate(CalculateRequest $request)
    {
        $result = $this->inflationService->calculate(
            $request->input('start_year'),
            $request->input('start_month'),
            $request->input('end_year'),
            $request->input('end_month'),
            $request->input('amount')
        );

        if (!$result) {
            return response()->json([
                'error' => 'Belirtilen tarihler için TÜFE verisi bulunamadı.'
            ], 404);
        }

        return response()->json($result);
    }
}
