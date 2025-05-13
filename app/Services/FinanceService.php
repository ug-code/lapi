<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FinanceService
{
    /**
     *  Finance fon getirisi verilerini alır
     *
     * @return array|mixed
     */
    /**
     *  Finance fon getirisi verilerini alır
     *
     * @return array|mixed
     */
    /**
     * Fintables'dan fon getirisi verilerini alır
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function fundsYield(): mixed
    {
        // İstek zaman aşımını önlemek için PHP çalışma süresini arttır
        set_time_limit(300);
        // Çevre değişkenlerini config() helper ile al
        $token =env('BROWSER_API_KEY', '');
        $browserApiUrl =   env('BROWSER_API_URL', '') . '/chrome/bql';

        // GraphQL sorgusu
        $query = $this->getFundsQuery();

        try {
            // URL'yi oluştur
            $url = $this->buildBrowserApiUrl($browserApiUrl, $token);

            // Laravel HTTP istemcisi ile istek gönder
            $response = $this->sendApiRequest($url, $query);

            // İstek başarısız oldu mu kontrol et
            if ($response->failed()) {
                return $this->formatErrorResponse('API isteği başarısız oldu', $response);
            }

            // JSON yanıtını al
            $responseData = $response->json();

            // Pre etiketi içindeki JSON verisini ayıkla
            return $this->extractJsonFromHtml($responseData);

        } catch (\Exception $e) {
            // Tüm hataları yakala ve ilgili hatayı kaydet
            \Log::error('Fon getirisi alınırken hata: ' . $e->getMessage(), [
                'exception' => $e,
                'class' => __CLASS__,
                'method' => __METHOD__
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Fintables fon getirisi için GraphQL sorgusunu döndürür
     *
     * @return string
     */
    private function getFundsQuery(): string
    {
        $financeApiUrl = env('FINANCE_API_URL') . "/funds/yield/";

        return <<<GRAPHQL
        mutation FormExample {
          goto(url: "$financeApiUrl") {
            status
          }
          verify(type: cloudflare) {
            solved
          },
            html {
            html
          }
        }
        GRAPHQL;
    }

    /**
     * Browser API URL'sini oluşturur
     *
     * @param string $baseUrl
     * @param string $token
     * @return string
     */
    private function buildBrowserApiUrl(string $baseUrl, string $token): string
    {
        return "$baseUrl?token=$token&proxy=residential&proxySticky=true&proxyCountry=tr&humanlike=true&blockConsentModals=true";
    }

    /**
     * API isteğini gönderir
     *
     * @param string $url
     * @param string $query
     * @return Response
     */
    private function sendApiRequest(string $url, string $query)
    {
        return Http::withOptions([
                'verify' => false, // SSL sertifika doğrulamasını devre dışı bırak
            ])
            ->timeout(180) // 3 dakika timeout
            ->retry(3, 1000) // 3 deneme, aralarında 1 saniye bekleyerek
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($url, [
                'query' => $query,
                'variables' => new \stdClass()
            ]);
    }

    /**
     * Hata yanıtını formatlar
     *
     * @param string $message
     * @param Response $response
     * @return array
     */
    private function formatErrorResponse(string $message, $response): array
    {
        return [
            'error'  => $message,
            'status' => $response->status(),
            'body'   => $response->body(),
        ];
    }

    /**
     * HTML içindeki pre etiketinden JSON verisini ayıklar
     *
     * @param array $responseData
     * @return array
     */
    private function extractJsonFromHtml(array $responseData): array
    {
        // HTML içerisindeki <pre> etiketindeki JSON verisini ayıkla
        if (isset($responseData['data']['html']['html'])) {
            $htmlContent = $responseData['data']['html']['html'];

            // <pre> etiketi içindeki içeriği al
            if (preg_match('/<pre>(.*?)<\/pre>/s', $htmlContent, $matches)) {
                $jsonString = $matches[1];

                // JSON'u düzgün bir şekilde ayrıştır
                $jsonData = json_decode($jsonString, true);

                if ($jsonData) {
                    return $jsonData;
                } else {
                    return [
                        'error' => 'JSON ayrıştırma hatası: ' . json_last_error_msg(),
                        'raw' => $jsonString
                    ];
                }
            }
        }

        return ['error' => 'Beklenen veri bulunamadı', 'response' => $responseData];
    }


}

