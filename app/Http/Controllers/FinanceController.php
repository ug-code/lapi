<?php

namespace App\Http\Controllers;

use App\Http\Requests\Finance\FundYieldRequest;
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
     * @param FundYieldRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function fundsYield(FundYieldRequest $request): JsonResponse
    {
        // Validasyon kontrolü geçtikten sonra parametreleri al
        $validated = $request->validated();
        // Arama, filtreleme ve sıralama parametrelerini al
        $search = $request->input('search');
        $filter = $request->collect('filter')->toArray();
        $sort = $request->input('sort');
        $sortDirection = (string) $request->input('direction', 'asc');

        $response = $this->financeService->fundsYield(
            search: $search,
            filter: $filter,
            sort: $sort,
            sortDirection: $sortDirection
        );

        return response()->json($response);
    }

}
