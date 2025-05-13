<?php

namespace App\Http\Controllers;

use App\Services\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function fundsYield(Request $request): JsonResponse
    {
        // Query parametrelerini alıp bir string olarak birleştir
        $queryParams = "";
        if ($request->getQueryString()) {
            $queryParams = "?" . $request->getQueryString();
        }

        $response = $this->financeService->fundsYield($queryParams);

        return response()->json($response);
    }

}
