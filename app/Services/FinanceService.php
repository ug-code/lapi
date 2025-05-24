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
     * @param string|null $sortDirection Sıralama yönü (asc/desc)
     * @return array
     * @throws \Exception
     */
    public function fundsYield(
        ?string $search = null,
        array   $filter = [],
        ?string $sort = null,
        ?string $sortDirection = 'asc',
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
     * @param string|null $sortDirection Sıralama yönü (asc/desc)
     * @return array|null
     */
    private function getFromCache(
        ?string $search = null,
        array   $filter = [],
        ?string $sort = null,
        ?string $sortDirection = 'asc'
    ): ?array
    {
        // Önce cache'te süresi dolmamış herhangi bir kayıt var mı kontrol et
        $hasCacheRecords = FundYield::where('expires_at', '>', now())->exists();

        // Eğer cache boşsa (süresi dolmuş tüm kayıtlar), null döndür
        if (!$hasCacheRecords) {
            return null;
        }

        // Cache varsa, filtrelemeye başla
        $query = FundYield::where('expires_at', '>', now());

        // Arama işlemi
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                    ->orWhere('title', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%")
                    ->orWhere('management_company_id', 'like', "%$search%")
                    ->orWhere('categories_id', 'like', "%$search%");
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
                } // management_company_id için dizi kontrolü
                elseif ($field === 'management_company_id' && is_array($value)) {
                    $query->whereIn($field, $value);
                } // categories_id için dizi kontrolü
                elseif ($field === 'categories_id' && is_array($value)) {
                    $query->whereIn($field, $value);
                } // Boolean değerler için (tefas)
                elseif ($field === 'tefas') {
                    $boolValue = $value ? 'true' : 'false';
                    $query->where($field, $boolValue);
                } // Normal eşitlik filtreleri
                else {
                    $query->where($field, $value);
                }
            }
        }

        // Sıralama
        if ($sort) {
            $direction = $sortDirection && strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sort, $direction);
        } else {
            // Varsayılan sıralama
            $query->latest();
        }

        // Sonuçları al
        $cacheRecords = $query->get()->toArray();

        // Cache varsa, filtre sonuçları boş olsa bile formatlanmış sonuç döndür
        return [
            "start"   => null,
            "end"     => null,
            "results" => $cacheRecords
        ];
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
                    $managementCompanyId = $fund['management_company_id'] ?? null;
                    $categoriesId = $fund['categories_id'] ?? $fund['categories__id'] ?? null;

                    // Eğer management_company_id bir dizi ise, ilk değeri al veya string'e dönüştür
                    if (is_array($managementCompanyId)) {
                        $managementCompanyId = !empty($managementCompanyId) ? $managementCompanyId[0] : null;
                    }

                    // Eğer categories_id bir dizi ise, ilk değeri al veya null olarak bırak
                    if (is_array($categoriesId)) {
                        $categoriesId = !empty($categoriesId) ? $categoriesId[0] : null;
                    }

                    $data[] = [
                        'code'                  => $fund['code'] ?? null,
                        'categories_id'         => $categoriesId,
                        'management_company_id' => $managementCompanyId,
                        'title'                 => $fund['title'] ?? null,
                        'type'                  => $fund['type'] ?? null,
                        'tefas'                 => $fund['tefas'] ? 'true' : 'false',
                        'yield_1m'              => $fund['yield_1m'] ?? 0,
                        'yield_3m'              => $fund['yield_3m'] ?? 0,
                        'yield_6m'              => $fund['yield_6m'] ?? 0,
                        'yield_ytd'             => $fund['yield_ytd'] ?? 0,
                        'yield_1y'              => $fund['yield_1y'] ?? 0,
                        'yield_3y'              => $fund['yield_3y'] ?? 0,
                        'yield_5y'              => $fund['yield_5y'] ?? 0,
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
