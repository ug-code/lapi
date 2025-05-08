<?php

namespace App\Http\Controllers;

use App\Services\FintableService;

class FintableController
{
    protected FintableService $fintableService;

    public function __construct(FintableService $fintableService)
    {

        $this->fintableService = $fintableService;
    }


    public function fundsYield($query = "")
    {
        return $this->fintableService->fundsYield();
    }

}
