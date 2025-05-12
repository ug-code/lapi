<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FinanceService
{
    /**
     * Browserless.io aracılığıyla Finance fon getirisi verilerini alır
     *
     * @return array|mixed
     */
    public function fundsYield()
    {
        $browserlessUrl = 'https://production-sfo.browserless.io/unblock';
        $token = env('BROWSER_API', '');

        $requestData = [
            'url' => 'https://api.finance.com/funds/yield/',
            'browserWSEndpoint' => false,
            'cookies' => false,
            'content' => true,
            'screenshot' => false,
            'proxy' => 'residential'
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("$browserlessUrl?token=$token", $requestData);

            if ($response->failed()) {
                return ['error' => 'API isteği başarısız oldu', 'status' => $response->status()];
            }

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

