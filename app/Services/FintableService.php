<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FintableService
{
    public function fundsYield()
    {
        $url = "https://api.fintables.com/funds/yield/";


        $headers = [
            'User-Agent' => 'Mozilla/5.0',
            'Cache-Control' => 'no-cache',
            'Postman-Token' => '42da548d-3ea3-4dd0-802d-f474b92028d0',
            'Host' => 'api.fintables.com',
            'Cookie' => '__cflb=02DiuEzSTdVZfbqT2K3FRv5P4qXEmqvrwtYGcMxH9ujMv; __cflb=02DiuEzSTdVZfbqT2K3FRv5P4qXEmqvrwtYGcMxH9ujMv',
            'Accept' => '*/*',
            'Connection' => 'keep-alive',
        ];
        try {
            $response = Http::withOptions(['verify' => false, 'allow_redirects' => true,])->withHeaders($headers)->get($url);

            if ($response->failed()) {
                return response()->json(['error' => 'API request failed'], 500);
            }

            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

