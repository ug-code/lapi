<?php

namespace App\Http\Controllers;


use App\Services\WeatherService;

class WeatherController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/weather/current",
     *   tags={"Weather"},
     *   summary="Get current weather of the Istanbul",
     *
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
    public function current()
    {
        return WeatherService::current();
    }


}
