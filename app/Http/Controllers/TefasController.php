<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class TefasController extends Controller
{
    /**
     * TEFAS API'sine istek atar ve sonucu döndürür
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getFundComparison(Request $request): JsonResponse
    {
        $url = 'https://www.tefas.gov.tr/api/DB/BindComparisonFundReturns';
        $postData = [
            'calismatipi' => '2',
            'fontip' => 'YAT',
            'sfontur' => '',
            'kurucukod' => '',
            'fongrup' => '',
            'bastarih' => 'Başlangıç',
            'bittarih' => 'Bitiş',
            'fonturkod' => '',
            'fonunvantip' => '',
            'strperiod' => '1,1,1,1,1,1,1',
            'islemdurum' => '1',
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Origin' => 'https://www.tefas.gov.tr',
            'Referer' => 'https://www.tefas.gov.tr/FonKarsilastirma.aspx',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->asForm()->post($url, $postData);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => 'TEFAS API isteği başarısız',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }
    }
} 