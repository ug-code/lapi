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
        $browserApiUrl = env('BROWSER_API_URL', '') . '/chrome/bql';

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
        $cacheRecord = FundYield::where('query_params', $queryParams)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($cacheRecord) {
          $responseData  =  $cacheRecord->response_data ?? null;

          if($responseData){
              return json_decode($cacheRecord->response_data,true);
          }

        }

        return null;
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
            // 1 gün sonra geçersiz olacak şekilde kaydet
            $fundYield                = new FundYield();
            $fundYield->fund_id       = 'cache_' . md5($queryParams); // Unique bir ID oluştur
            $fundYield->query_params  = $queryParams;
            $fundYield->response_data = json_encode($result);
            $fundYield->expires_at    = now()->addDay();
            $fundYield->save();


            // Eski cache kayıtlarını temizle (opsiyonel)
            //  $this->cleanupExpiredCache();

        } catch (\Exception $e) {
            // Cache kaydetme hatası kritik değil, sadece logla ve devam et
            \Log::warning('Cache kaydetme hatası: ' . $e->getMessage(), [
                'exception' => $e
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
        // Ortam kontrolü yaparak SSL doğrulamasını yönet
        $httpOptions = [];

        // Prod değilse SSL doğrulamasını devre dışı bırak
       // if (app()->environment() !== 'prod') {
            $httpOptions['verify'] = false;
        //}

        // Http istemcisini yapılandır
        return Http::withOptions($httpOptions)
            ->connectTimeout(0)
            ->timeout(0)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($url, [
                'query' => $query,
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
                        'raw'   => $jsonString
                    ];
                }
            }
        }

        return ['error'    => 'Beklenen veri bulunamadı',
                'response' => $responseData];
    }


}
