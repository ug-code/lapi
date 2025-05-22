<?php

namespace App\Services;

use App\Models\FundYield;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * Fintables'dan fon getirisi verilerini alır, cache uygulayarak
     *
     * @param string $queryParams İsteğe bağlı olarak eklenecek query parametreleri
     * @return array|mixed
     * @throws \Exception
     */
    public function fundsYield(string $queryParams = ""): mixed
    {
        // Cache'ten veriyi kontrol et
        $cachedData = $this->getFromCache($queryParams);
        if ($cachedData) {
            return $cachedData;
        }

        // İstek zaman aşımını önlemek için PHP çalışma süresini arttır

        // Çevre değişkenlerini config() helper ile al
        $token         = env('BROWSER_API_KEY', '');
        $browserApiUrl = env('BROWSER_API_URL', '') . '/chromium/bql';

        // GraphQL sorgusu (query parametrelerini de geçiriyoruz)
        $query = $this->getFundsQuery($queryParams);

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
            $result = $this->extractJsonFromHtml($responseData);

            // Sonucu veritabanına cache'le
            $this->cacheResult($queryParams, $result);

            return $result;

        } catch (\Exception $e) {
            // Tüm hataları yakala ve ilgili hatayı kaydet
            \Log::error('Fon getirisi alınırken hata: ' . $e->getMessage(), [
                'exception' => $e,
                'class'     => __CLASS__,
                'method'    => __METHOD__
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Cache'ten veriyi al
     *
     * @param string $queryParams
     * @return array|null
     */
    private function getFromCache(string $queryParams): ?array
    {
        $cacheRecord = FundYield::where('expires_at', '>', now())
            ->latest()
            ->get()->toArray();

        if ($cacheRecord) {
            $formatedCacheRecord = [
                "start"   => null,
                "end"     => null,
                "results" => $cacheRecord
            ];

            return $formatedCacheRecord;

        }

        return [];
    }

    /**
     * Sonucu veritabanına cache'le
     *
     * @param string $queryParams
     * @param array $result
     * @return void
     */
    private function cacheResult(string $queryParams, array $result): void
    {
        try {
            // Sonuç içinde results anahtarı var mı kontrol et
            $results = $result['results'] ?? null;
            if (isset($results) && is_array($results) && !empty($results)) {
                $data = [];
                foreach ($results as $fund) {
                    $data[] = [
                        'fund_id'               => 'cache_' . md5($queryParams),
                        'code'                  => $fund['code'] ?? null,
                        'management_company_id' => $fund['management_company_id'] ?? null,
                        'title'                 => $fund['title'] ?? null,
                        'type'                  => $fund['type'] ?? null,
                        'tefas'                 => $fund['tefas'] ? 'true' : 'false',
                        'yield_1m'              => $fund['yield_1m'] ?? null,
                        'yield_3m'              => $fund['yield_3m'] ?? null,
                        'yield_6m'              => $fund['yield_6m'] ?? null,
                        'yield_ytd'             => $fund['yield_ytd'] ?? null,
                        'yield_1y'              => $fund['yield_1y'] ?? null,
                        'yield_3y'              => $fund['yield_3y'] ?? null,
                        'yield_5y'              => $fund['yield_5y'] ?? null,
                        'expires_at'            => now()->addDay(),
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ];
                }

                FundYield::insert($data);
            } else {
                // Sonuç yapısında 'results' yoksa, bir hata kaydı oluştur
                \Log::warning('Fon getirisi verisi beklenen formatta değil', [
                    'result' => $result
                ]);
            }

            // Eski cache kayıtlarını temizle (opsiyonel)
            // $this->cleanupExpiredCache();

        } catch (\Exception $e) {
            // Cache kaydetme hatası kritik değil, sadece logla ve devam et
            \Log::warning('Cache kaydetme hatası: ' . $e->getMessage(), [
                'exception'    => $e,
                'query_params' => $queryParams
            ]);
        }
    }

    /**
     * Süresi dolmuş cache kayıtlarını temizle
     *
     * @return void
     */
    private function cleanupExpiredCache(): void
    {
        // Haftada bir kez çalışacak şekilde rasteleştir (performans için)
        if (rand(1, 100) <= 15) {
            FundYield::where('expires_at', '<', now())
                ->where('query_params', '!=', null)
                ->limit(500) // Bir seferde çok fazla silme işlemi yapma
                ->delete();
        }
    }

    /**
     * Fintables fon getirisi için GraphQL sorgusunu döndürür
     *
     * @param string $queryParams İsteğe bağlı olarak eklenecek query parametreleri
     * @return string
     */
    private function getFundsQuery(string $queryParams = ""): string
    {
        $financeApiUrl = env('FINANCE_API_URL') . "/funds/yield/" . $queryParams;

        return <<<GRAPHQL
        mutation FormExample {
          goto(url: "$financeApiUrl") {
            status
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
        return "$baseUrl?token=$token";
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
        try {
            $httpOptions = [
                'verify' => false,
                // SSL doğrulamasını kapat
            ];

            return Http::withOptions($httpOptions)
                ->connectTimeout(0)
                ->timeout(0)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($url, [
                    'query' => $query,
                ])->throw(); // Burada RequestException fırlatılır


        } catch (\Exception $e) {
            \Log::error('HTTP İsteği Hatası: ' . $e->getMessage());
            throw $e;
        }
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
                        'raw'   => $jsonString
                    ];
                }
            }
        }

        return ['error'    => 'Beklenen veri bulunamadı',
                'response' => $responseData];
    }


}
