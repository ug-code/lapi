<?php

namespace App\Services;

use App\Models\FundYield;
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
     * Fintables'dan fon getirisi verilerini alır, cache uygulayarak
     *
     * @param string|null $search Arama metni
     * @param array $filter Filtreleme kriterleri
     * @param string|null $sort Sıralama alanı
     * @param string $sortDirection Sıralama yönü (asc/desc)
     * @return array
     * @throws \Exception
     */
    public function fundsYield(
        ?string $search = null,
        array   $filter = [],
        ?string $sort = null,
        string  $sortDirection = 'asc',
    ): array
    {
        // Cache'ten veriyi kontrol et
        $cachedData = $this->getFromCache($search, $filter, $sort, $sortDirection);
        if ($cachedData) {
            return $cachedData;
        }

        // İstek zaman aşımını önlemek için PHP çalışma süresini arttır

        // Çevre değişkenlerini config() helper ile al
        $token         = env('BROWSER_API_KEY', '');
        $browserApiUrl = env('BROWSER_API_URL', '') . '/chromium/bql';

        // GraphQL sorgusu (query parametrelerini de geçiriyoruz)
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
            $result = $this->extractJsonFromHtml($responseData);

            // Sonucu veritabanına cache'le
            $this->cacheResult($result);

            // API'den sonuç aldıktan sonra tekrar cache'i arama ve filtreleme ile sorgula
            $cachedData = $this->getFromCache($search, $filter, $sort, $sortDirection);
            if ($cachedData) {
                return $cachedData;
            }

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
     * Cache'ten veriyi al, veritabanı seviyesinde arama, filtreleme ve sıralama uygula
     *
     * @param string|null $search Arama metni
     * @param array $filter Filtreleme kriterleri
     * @param string|null $sort Sıralama alanı
     * @param string $sortDirection Sıralama yönü (asc/desc)
     * @return array|null
     */
    private function getFromCache(
        ?string $search = null,
        array   $filter = [],
        ?string $sort = null,
        string  $sortDirection = 'asc'
    ): ?array
    {

        // Temel sorgu - sadece süresi dolmamış kayıtları al
        $query = FundYield::where('expires_at', '>', now());

        $cacheRecord = FundYield::where('expires_at', '>', now())
            ->latest()
            ->get()->toArray();

        if ($cacheRecord) {
            $formatedCacheRecord = [
                "start"   => null,
                "end"     => null,
                "results" => $cacheRecord
            ];
        }

        // Arama işlemi
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                    ->orWhere('title', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%")
                    ->orWhere('management_company_id', 'like', "%$search%");
            });
        }


        // Filtreleme işlemi
        if (!empty($filter)) {
            foreach ($filter as $field => $value) {
                // Getiri aralıkları için özel filtreleme
                if (str_starts_with($field, 'yield_') && is_array($value)) {
                    // Minimum değer filtresi
                    if (isset($value['min'])) {
                        $query->where($field, '>=', $value['min']);
                    }

                    // Maksimum değer filtresi
                    if (isset($value['max'])) {
                        $query->where($field, '<=', $value['max']);
                    }
                } // Boolean değerler için (tefas)
                elseif ($field === 'tefas') {
                    $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $query->where($field, $boolValue);
                } // Normal eşitlik filtreleri
                else {
                    $query->where($field, $value);
                }
            }
        }

        // Sıralama
        if ($sort) {
            $direction = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sort, $direction);
        } else {
            // Varsayılan sıralama
            $query->latest();
        }

        // Sonuçları al
        $cacheRecords = $query->get()->toArray();

        if ($cacheRecords) {
            $formatedCacheRecord = [
                "start"   => null,
                "end"     => null,
                "results" => $cacheRecords
            ];

            return $formatedCacheRecord;
        }

        return [];
    }

    /**
     * Sonucu veritabanına cache'le
     *
     * @param array $result
     * @return void
     */
    private function cacheResult(array $result): void
    {
        try {
            // Sonuç içinde results anahtarı var mı kontrol et
            $results = $result['results'] ?? null;
            if (isset($results) && is_array($results) && !empty($results)) {
                $data = [];
                foreach ($results as $fund) {
                    $data[] = [

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
                'exception' => $e,
            ]);
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
    private function sendApiRequest(string $url, string $query): Response
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
