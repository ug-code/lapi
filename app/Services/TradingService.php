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
}
