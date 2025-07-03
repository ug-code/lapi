<?php

namespace App\Services;

use App\Models\FundYield;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Helpers\ApiHelper;
use App\Repositories\FundYieldRepository;

class FinanceService
{
    private FundYieldRepository $fundYieldRepository;

    public function __construct(FundYieldRepository $fundYieldRepository)
    {
        $this->fundYieldRepository = $fundYieldRepository;
    }

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
        int $page = 1,
        int $perPage = 20
    ): array
    {
        // Cache'ten veriyi kontrol et
        $cachedData = $this->fundYieldRepository->paginateFundYieldFromCache($search, $filter, $sort, $sortDirection, $page, $perPage);
        if ($cachedData) {
            return $cachedData;
        }

        // İstek zaman aşımını önlemek için PHP çalışma süresini arttır

        // Çevre değişkenlerini config() helper ile al
        $token         = env('BROWSER_API_KEY', '');
        $browserApiUrl = env('BROWSER_API_URL', '') . '/chromium/bql';

        // GraphQL sorgusu (query parametrelerini de geçiriyoruz)
        $query = $this->getFundYieldGraphQLQuery();

        try {
            // URL'yi oluştur
            $url = ApiHelper::buildBrowserApiUrl($browserApiUrl, $token);

            // Laravel HTTP istemcisi ile istek gönder
            $response = ApiHelper::sendApiRequest($url, $query);

            // İstek başarısız oldu mu kontrol et
            if ($response->failed()) {
                return ApiHelper::formatErrorResponse('API isteği başarısız oldu', $response);
            }

            // JSON yanıtını al
            $responseData = $response->json();

            // Pre etiketi içindeki JSON verisini ayıkla
            $result = ApiHelper::extractJsonFromHtml($responseData);

            // Sonucu veritabanına cache'le
            $this->fundYieldRepository->cacheFundYieldResult($result);

            // API'den sonuç aldıktan sonra tekrar cache'i arama ve filtreleme ile sorgula
            $cachedData = $this->fundYieldRepository->paginateFundYieldFromCache($search, $filter, $sort, $sortDirection, $page, $perPage);
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
     * Fintables fon getirisi için GraphQL sorgusunu döndürür
     *
     * @return string
     */
    private function getFundYieldGraphQLQuery(): string
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

}
