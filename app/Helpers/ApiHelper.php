<?php

namespace App\Helpers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiHelper
{
    public static function formatErrorResponse(string $message, $response): array
    {
        return [
            'error'  => $message,
            'status' => $response->status(),
            'body'   => $response->body(),
        ];
    }

    public static function extractJsonFromHtml(array $responseData): array
    {
        if (isset($responseData['data']['html']['html'])) {
            $htmlContent = $responseData['data']['html']['html'];
            if (preg_match('/<pre>(.*?)<\/pre>/s', $htmlContent, $matches)) {
                $jsonString = $matches[1];
                $jsonData = json_decode($jsonString, true);
                if ($jsonData) {
                    return $jsonData;
                } else {
                    return [
                        'error' => 'JSON ayrıştırma hatası: ' . json_last_error_msg(),
                        'raw'   => $jsonString
                    ];
                }
            }
        }
        return ['error' => 'Beklenen veri bulunamadı', 'response' => $responseData];
    }

    public static function sendApiRequest(string $url, string $query): Response
    {
        $httpOptions = [
            'verify' => false,
        ];
        return Http::withOptions($httpOptions)
            ->connectTimeout(0)
            ->timeout(0)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($url, [
                'query' => $query,
            ])->throw();
    }

    public static function buildBrowserApiUrl(string $baseUrl, string $token): string
    {
        return "$baseUrl?token=$token";
    }
} 