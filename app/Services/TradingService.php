<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TradingService
{
    public function scan($body)
    {
        /**
         * curl 'https://scanner.tradingview.com/turkey/scan' \
         * --data-raw '{"filter":[{"left":"change","operation":"nempty"},{"left":"EMA200","operation":"crosses_below","right":"close"},{"left":"is_primary","operation":"equal","right":true}],"options":{"active_symbols_only":true,"lang":"tr"},"symbols":{"query":{"types":[]},"tickers":[]},"columns":["logoid","name","close","change","change_abs","Recommend.All","volume","market_cap_basic","price_earnings_ttm","earnings_per_share_basic_ttm","number_of_employees","sector","description","type","subtype","update_mode","pricescale","minmov","fractional","minmove2","currency","fundamental_currency_code"],"sort":{"sortBy":"change","sortOrder":"desc"},"price_conversion":{"to_symbol":false},"range":[0,150]}' \
         * --compressed
         */
        //chart url example https://tr.tradingview.com/chart/?symbol=BIST%3AFENER
        $url      = "https://scanner.tradingview.com/turkey/scan";
        $result   = [];
        $response = Http::withOptions(['verify' => false])
                        ->post($url, $body)
                        ->json();

        if (!$response) {
            return null;
        }
        $result['totalCount'] = $response['totalCount'];

        foreach ($response['data'] as $row) {
            $result['data'][] = [
                'symbol'            => $row['s'],
                'yearlyPerformance' => $row['d']['0'],
                'link'              => "https://tr.tradingview.com/chart/?symbol=" . $row['s'],


            ];
        }


        return $result;

    }

    public function kapBuySellNotifitions()
    {
        /**
         * curl 'https://www.kap.org.tr/tr/api/memberDisclosureQuery' \
         * -H 'Connection: keep-alive' \
         * -H 'Pragma: no-cache' \
         * -H 'Cache-Control: no-cache' \
         * -H 'sec-ch-ua: "Chromium";v="94", "Google Chrome";v="94", ";Not A Brand";v="99"' \
         * -H 'Accept: application/json, text/plain, ' \
         *
         * -H 'Content-Type: application/json;charset=UTF-8' \
         * -H 'sec-ch-ua-mobile: ?0' \
         * -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36' \
         * -H 'sec-ch-ua-platform: "Windows"' \
         * -H 'Origin: https://www.kap.org.tr' \
         * -H 'Sec-Fetch-Site: same-origin' \
         * -H 'Sec-Fetch-Mode: cors' \
         * -H 'Sec-Fetch-Dest: empty' \
         * -H 'Referer: https://www.kap.org.tr/tr/bildirim-sorgu' \
         * -H 'Accept-Language: en-US,en;q=0.9' \
         * -H 'Cookie: KAP=UOFdXLvBOPJmHAXCFTt0x/7cUxY0002; NSC_xxx.lbq.psh.us_tjuf=14b5a3d9cd687b3ab9713c09dde158706cf75127871f6bee3914e165062bf67f7daa44a6; KAP_.kap.org.tr_%2F_wlf=AAAAAAWyz1va1VjGkpKnSd2Gsa7sLLmZqxuh_imoitvAFfiuV8czsiaYf15o_A8kFNLQZVHXEtB6uby69Q64UbRJPjgBwvcUMaaERajk4R0_pWXZ6w==&' \
         * --data-raw '{"fromDate":"2020-10-14","toDate":"2021-10-14","year":"","prd":"","term":"","ruleType":"","bdkReview":"","disclosureClass":"ODA","index":"","market":"","isLate":"","subjectList":["8aca490d50286f620150287614ae005c"],"mkkMemberOidList":[],"inactiveMkkMemberOidList":[],"bdkMemberOidList":[],"mainSector":"","sector":"","subSector":"","memberType":"IGS","fromSrc":"N","srcCategory":"","discIndex":[]}' \
         * --compressed
         */
        $url    = "https://www.kap.org.tr/tr/api/memberDisclosureQuery";


        $body     = [
            "fromDate"                 => "2020-10-14",
            "toDate"                   => "2021-10-14",
            "year"                     => "",
            "prd"                      => "",
            "term"                     => "",
            "ruleType"                 => "",
            "bdkReview"                => "",
            "disclosureClass"          => "ODA",
            "index"                    => "",
            "market"                   => "",
            "isLate"                   => "",
            "subjectList"              => [
                "8aca490d50286f620150287614ae005c"
            ],
            "mkkMemberOidList"         => [],
            "inactiveMkkMemberOidList" => [],
            "bdkMemberOidList"         => [],
            "mainSector"               => "",
            "sector"                   => "",
            "subSector"                => "",
            "memberType"               => "IGS",
            "fromSrc"                  => "N",
            "srcCategory"              => "",
            "discIndex"                => []
        ];
        $response = Http::withOptions(['verify' => false])
            ->post($url, $body)
            ->json();

        if (!$response) {
            return null;
        }



        return $response;
    }

}
