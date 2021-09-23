<?php

namespace App\Http\Controllers;


use App\Services\TradingService;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class TradingController extends Controller
{
    protected $tradingService;

    public function __construct(TradingService $tradingService)
    {

        $this->tradingService = $tradingService;
    }

    /**
     * @OA\Get(
     * path="/api/v1/trading/cheap",
     *   tags={"Trading"},
     *   summary="How to find cheap stocks (Daily,Weekly,Monthly SMA200 order by volume)",
     *  @OA\Parameter(
     *         name="interval",
     *         in="query",
     *         description="interval values that need to be considered for filter 1W=weekly,1M=Monthly,Empty Daily ",
     *         @OA\Schema(
     *         type="array",
     *           @OA\Items(
     *               type="string",
     *               enum={"1W","1M"},
     *               default="1W"
     *           ),
     *         ),
     *         style="form"
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *      )
     *   )
     * )
     *
     */
    public function cheap(Request $request)
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


}
