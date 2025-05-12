<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FinanceService
{
    /**
     *  Finance fon getirisi verilerini alır
     *
     * @return array|mixed
     */
    public function fundsYield(): mixed
    {
        $browserApiUrl =  env('BROWSER_API_URL', '').'/unblock';
        $token = env('BROWSER_API_KEY', '');

        $requestData = [
            'url' =>  env('FINANCE_API_URL', '').'/funds/yield/',
            'browserWSEndpoint' => false,
            'cookies' => false,
            'content' => true,
            'screenshot' => false,
            'proxy' => 'residential'
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("$browserApiUrl?token=$token", $requestData);

            if ($response->failed()) {
                return ['error' => 'API isteği başarısız oldu', 'status' => $response->status()];
            }

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

