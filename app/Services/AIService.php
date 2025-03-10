<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{

    public function chatWithAI($body)
    {


        $url = "https://api.openai.com/v1/chat/completions";



        $apiKey = env('NEXT_PUBLIC_OPENAI_API_KEY');

        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
            'Cookie'        => '__cf_bm=7numayYsF8UkFNEnbaHwrsb6mWlnEcxYKh62PmbUIgU-1741624403-1.0.1.1-Q7TGCpzbve_SJvVcfNgEJWQXSiHKr_psWZ6L40WCD_8JsT7NHb9WCeHnQpRvqInkkKKT5qEnqxPCzeFvvJ_PenuSLg_QPGI8oZqb.t8hHX4; _cfuvid=aRPRPsr98dHgEQPTr4ExG47669l43gdXQqiYVRf0qow-1741624403949-0.0.1.1-604800a000'
        ];

        $response = Http::withHeaders($headers)->withOptions(['verify' => false])
            ->post($url, $body)
            ->json();

        return $response;
    }
}
