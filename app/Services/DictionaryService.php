<?php

namespace App\Services;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;

class DictionaryService
{
    public function scan($keyword)
    {

        $url = "https://api.dictionaryapi.dev/api/v2/entries/en/$keyword";

        $response = Http::withOptions(['verify' => false])
            ->get($url)
            ->json();

        return $response;
    }


}
