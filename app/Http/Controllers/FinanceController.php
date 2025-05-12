<?php

namespace App\Http\Controllers;

use App\Services\FinanceService;
use Illuminate\Http\JsonResponse;

class FinanceController
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {

        $this->financeService = $financeService;
    }


    /**
     * Fon getirisi bilgilerini döndürür.
     *
     * @param string $query
     * @return JsonResponse
     */
    public function fundsYield($query = ""): JsonResponse
    {
        $response = $this->financeService->fundsYield();
        return response()->json($response);
    }

}
