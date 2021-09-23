<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{

    public static function current($city = "istanbul", $stateCode = "tr")
    {
        /**
         * https://api.openweathermap.org/data/2.5/weather?q=istanbul,tr&appid={APIKEY}&lang=tr&units=metric
         */
        $apiKey = env('WHEATHER_APIKEY', "");
        $url    = "https://api.openweathermap.org/data/2.5/weather?q=$city,$stateCode&appid=$apiKey&lang=tr&units=metric";

        return Http::withOptions(['verify' => false])
                   ->get($url)->json();


    }
}
