<?php

namespace App\Http\Controllers;


use App\Services\TradingService;
use Illuminate\Http\Request;

class TradingController extends Controller
{
    protected TradingService $tradingService;

    public function __construct(TradingService $tradingService)
    {

        $this->tradingService = $tradingService;
    }


    public function cheap(Request $request): ?array
    {

        $interval = $request->query('interval');


        $interval = $interval ? '|' . $interval : '';

        $body = [
            "filter"           => [
                [
                    "left"      => "change" . $interval,
                    "operation" => "nempty"
                ],
                [
                    "left"      => "SMA200" . $interval,
                    "operation" => "crosses_below",
                    "right"     => "close" . $interval
                ],
                [
                    "left"      => "is_primary",
                    "operation" => "equal",
                    "right"     => true
                ]
            ],
            "options"          => [
                "active_symbols_only" => true,
                "lang"                => "tr"
            ],
            "symbols"          => [
                "query"   => [
                    "types" => []
                ],
                "tickers" => []
            ],
            "columns"          => [
                "Perf.Y",
            ],
            "sort"             => [
                "sortBy"    => "volume" . $interval,
                "sortOrder" => "desc"
            ],
            "price_conversion" => [
                "to_symbol" => false
            ],
            "range"            => [
                0,
                150
            ]
        ];

        return $this->tradingService->scan($body);

    }


    public function kapBuySellNotifitions()
    {
        return $this->tradingService->kapBuySellNotifitions();
    }


}
