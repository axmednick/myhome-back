<?php

namespace App\Console\Commands;

use App\Models\SentMessage;
use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;
use simplehtmldom\HtmlDomParser;

class ScrapeTapAz extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tap-az';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tap.az saytından elanları çəkir və elan sahibinin adını ilə telefon nömrəsini ekrana yazdırır';

    public function __construct(protected WhatsappService $whatsappService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $baseUrl = 'https://tap.az';
        $listingUrl = $baseUrl . '/elanlar/dasinmaz-emlak';

        // Guzzle client ilə HTTP sorğusu aparırıq
        $guzzle = new GuzzleClient([
            'verify'  => false, // Test məqsədilə SSL doğrulamasını deaktiv edirik
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36'
            ],
            'timeout' => 30,
        ]);

        // Listing səhifəsini əldə edirik
        try {
            $response = $guzzle->get($listingUrl);
            $htmlContent = $response->getBody()->getContents();
            $listingHtml = HtmlDomParser::str_get_html($htmlContent);
        } catch (\Exception $e) {
            $this->error("Səhifə yüklənərkən xəta: " . $e->getMessage());
            return 1;
        }

        if (!$listingHtml) {
            $this->error("Listing səhifəsi əldə oluna bilmədi: {$listingUrl}");
            return 1;
        }

        // Elan linklərini tapırıq: href atributunda elan linki var.
        $links = $listingHtml->find('a.products-link[data-stat="ad-card-link"]');
        if (!$links) {
            $this->info("Elan linkləri tapılmadı.");
            return 0;
        }

        foreach ($links as $link) {
            $relativeLink = $link->href;
            $fullLink = $baseUrl . $relativeLink;
            $this->info("Emal edilir: {$fullLink}");

            // Elanın səhifəsini yükləyirik
            try {
                $adResponse = $guzzle->get($fullLink);
                $adHtmlContent = $adResponse->getBody()->getContents();
                $adHtml = HtmlDomParser::str_get_html($adHtmlContent);
            } catch (\Exception $e) {
                $this->error("Elan səhifəsi əldə oluna bilmədi: {$fullLink}. Xəta: " . $e->getMessage());
                continue;
            }

            if (!$adHtml) {
                $this->error("Elan səhifəsi parse edilə bilmədi: {$fullLink}");
                continue;
            }

            // Elan sahibinin adını çıxarırıq
            $ownerDiv = $adHtml->find('div.product-owner__info-name', 0);
            if (!$ownerDiv) {
                $this->error("Elan sahibinin adı tapılmadı: {$fullLink}");
                continue;
            }
            $ownerName = trim($ownerDiv->plaintext);

            // URL-dən elanın ID-sini çıxarırıq (məsələn: /elanlar/dasinmaz-emlak/torpaq-sahesi/41838338)
            $parts = explode('/', trim($relativeLink, '/'));
            $adId = end($parts);
            if (empty($adId)) {
                $this->error("Elan ID tapılmadı: {$fullLink}");
                continue;
            }

            // API URL: elanın ID-sinə əsaslanır
            $apiUrl = $baseUrl . "/ads/{$adId}/phones";

            // API-ə POST request göndəririk
            try {
                $phoneResponse = Http::post($apiUrl);
            } catch (\Exception $e) {
                $this->error("Elan ID {$adId} üçün telefon məlumatı əldə oluna bilmədi. Xəta: " . $e->getMessage());
                continue;
            }

            if (!$phoneResponse->successful()) {
                $this->error("Elan ID {$adId} üçün telefon məlumatı əldə oluna bilmədi. HTTP status: " . $phoneResponse->status());
                continue;
            }

            $phoneData = $phoneResponse->json();
            if (!isset($phoneData['phones']) || !is_array($phoneData['phones']) || empty($phoneData['phones'])) {
                $this->error("Elan ID {$adId} üçün telefon nömrəsi tapılmadı.");
                continue;
            }

            $phoneNumber = $this->localToInternational($phoneData['phones'][0]);

            $sentMessage = SentMessage::where('phone', $phoneNumber)->first();

            $message = "Salam, {$ownerName}\n\nElanınızı Tap.az platformasında gördük. Daha çox potensial müştəriyə çatmaq istəyirsiniz?\n\nMyHome.az-da elanınızı tamamilə ödənişsiz yerləşdirərək minlərlə daşınmaz əmlak alıcısına çatdıra bilərsiniz!\n\nSatış prosesinizi sürətləndirmək üçün elə indi MyHome.az saytına daxil olaraq elanınızı yerləşdirin.\n\nSualınız olarsa, bizə yaza bilərsiniz.";

            if (!$sentMessage) {
                $response = $this->whatsappService->sendMessage(
                    921372965,
                    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3NDQwMzMyMzksInVzZXJfaWQiOjI4MjR9.DZ2-w3eaku_CB9pC7O2PwSx4g3uScroDyv0vYouZw-I',
                    $phoneNumber,
                    $message
                );
            }
        }

        return 0;
    }

    /**
     * Nömrəni yerli formatdan beynəlxalq formata çevirir.
     *
     * @param string $phone
     * @return string
     */
    private function localToInternational(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone); // Yalnız rəqəmləri saxlayır
        if (substr($digits, 0, 1) === '0') {
            $digits = substr($digits, 1);
        }
        return '+994' . $digits;
    }
}
