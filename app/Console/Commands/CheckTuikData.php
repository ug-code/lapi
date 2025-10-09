<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\TuikMonthlyData;

class CheckTuikData extends Command
{
    protected $signature = 'evds:check';
    protected $description = 'TCMB EVDS verilerini kontrol eder ve yenilerini tuik_monthly_data tablosuna kaydeder.';

    public function handle()
    {
        $this->info('EVDS verisi kontrol ediliyor...');

        // POST form verilerini oluştur
        $formData = [
            'thousand' => 1,
            'decimal' => 2,
            'frequency' => 'MONTH',
            'aggregationType' => 'avg',
            'formula' => 0,
            'graphicType' => 0,
            'skip' => 0,
            'take' => 12,
            'toExcel' => true,
            'select' => 'TP.FE.OKTG01-0',
            'startDate' => '01-01-2003',
            'endDate' => '30-09-2025',
            'obsCountEnabled' => '',
            'obsCount' => '',
            'categories' => 5939,
            'mongoAdresses' => 'evds',
            'userId' => '',
            'datagroupString' => 'bie_feoktg',
            'dateFormatValue' => 'yyyy-ww',
            'customFormula' => 'null',
            'excludedSeries' => 'null',
        ];

        // Laravel'in form parametrelerini query-string formatına dönüştür
        $bodyString = http_build_query($formData);

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Host' => 'evds2.tcmb.gov.tr',
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Content-Length' => strlen($bodyString),
                    'Cookie' => 'SessionCookie=!TyY5v7Q+LyM/NQV0ZjTl3bylM4fwcMJdUWShIypDNRQSbzmtHn8HsfrlQXl4iiOa5qpUKItLhIoxENs=; TS013c5758=015d31d6917f10c7b47b794dcb137c2d6e65229de51fb213fcbb451735a5fde936790ba8f27f2ca363e75e0b60ef97054a972ae4a915ad1e6a22dbd8f6d77f5a2a888ae5b9e7d6b49672f2d0cff84186232340ab7f; TS01ba0984=015d31d6918584b5810ffdb3b1e87e6a72adbb1a5858f6784bbd909325132cfa98dba06f048e075a3aeb421ac1818f7f3c3b3afeae64816e27cff3a8b0331783e62f1621ab',
                ])
                ->withBody($bodyString, 'application/x-www-form-urlencoded; charset=UTF-8')
                ->post('https://evds2.tcmb.gov.tr/EVDSServlet');

        } catch (\Exception $e) {
            $this->error("İstek atılamadı: " . $e->getMessage());
            return 1;
        }

        // 1️⃣ Hata durumu — 500, 403, 404 gibi
        if ($response->failed()) {
            $this->error('EVDS isteği başarısız: ' . $response->status());

            // Ek bilgi
            $this->line('--- Yanıt Gövdesi (Body) ---');
            $this->line(substr($response->body(), 0, 1000)); // sadece ilk 1000 karakter

            $this->line('--- Header Bilgileri ---');
            foreach ($response->headers() as $key => $value) {
                $this->line($key . ': ' . implode(', ', $value));
            }

            return 1;
        }

        // 2️⃣ Başarılı durum
        $data = $response->json();
        if (!$data || empty($data['items'])) {
            $this->warn('Yeni veri bulunamadı veya format hatalı.');
            $this->line('Gelen veri tipi: ' . gettype($response->body()));
            $this->line('İlk 500 karakter: ' . substr($response->body(), 0, 500));
            return 0;
        }

        // 3️⃣ Veri işleme
        $newCount = 0;
        foreach ($data['items'] as $item) {
            if (!isset($item['Tarih'], $item['TP_FE_OKTG01'])) continue;

            [$year, $month] = explode('-', $item['Tarih']);
            $year = (int) $year;
            $month = (int) $month;
            $value = (float) $item['TP_FE_OKTG01'];

            $exists = TuikMonthlyData::where('year', $year)
                ->where('month', $month)
                ->exists();

            if (!$exists) {
                TuikMonthlyData::create([
                    'year' => $year,
                    'month' => $month,
                    'value' => $value,
                ]);
                $this->info("Yeni veri eklendi: $year-$month = $value");
                $newCount++;
            }
        }

        $this->info("$newCount yeni kayıt eklendi.");
        return 0;
    }
}
