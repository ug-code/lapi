<?php

namespace App\Http\Controllers;


use App\Services\PdfService;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    protected PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {

        $this->pdfService = $pdfService;
    }


    public function test()
    {
        $config = new \Smalot\PdfParser\Config();

        $config->setFontSpaceLimit(0);
$parser = new \Smalot\PdfParser\Parser([], $config);
        
        $pdf = $parser->parseContent(file_get_contents('https://www.kap.org.tr/tr/api/file/download/4028328d906f407c0190825cb8304880'));

        $allPages = [];
        foreach ($pdf->getPages() as $page) {
            $text = $page->getText();
            // Özel karakter düzeltmeleri
            $text = str_replace('Ý', 'İ', $text);
            $text = str_replace('ý', 'ı', $text);
            $text = str_replace('Ð', 'Ğ', $text);
            $text = str_replace('ð', 'ğ', $text);
            $text = str_replace('Þ', 'Ş', $text);
            $text = str_replace('þ', 'ş', $text);
            $text = str_replace('Ü', 'Ü', $text);
            $text = str_replace('ü', 'ü', $text);
            $text = str_replace('Ö', 'Ö', $text);
            $text = str_replace('ö', 'ö', $text);
            $text = str_replace('Ç', 'Ç', $text);
            $text = str_replace('ç', 'ç', $text);
            
            // Satırları ayır ve her satıra yeni satır ekle
            $lines = explode("\n", $text);
            $formattedLines = [];
            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    $formattedLines[] = $line . "\nBu bir yeni satırdır\n";
                }
            }
            
            $allPages[] = implode("", $formattedLines);
        }

        return implode("\n--- Yeni Sayfa ---\n\n", $allPages);
    }


}
