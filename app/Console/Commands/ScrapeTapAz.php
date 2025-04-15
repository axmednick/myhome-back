<?php

namespace App\Console\Commands;

use App\Models\SentMessage;
use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use simplehtmldom\HtmlWeb;

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

    /**
     * Execute the console command.
     *
     * @return int
     */


    public function __construct(protected WhatsappService $whatsappService)
    {
        parent::__construct();

    }

    public function handle()
    {
        $baseUrl = 'https://tap.az';
        $listingUrl = $baseUrl . '/elanlar/dasinmaz-emlak';

        $client = new HtmlWeb();
        $listingHtml = $client->load($listingUrl);


        if (!$listingHtml) {
            $this->error("Listing səhifəsi əldə oluna bilmədi: {$listingUrl}");
            return 1;
        }

        // Elanların linklərini götürürük: href atributunda elan linki var.
        $links = $listingHtml->find('a.products-link[data-stat="ad-card-link"]');

        if (!$links) {
            $this->info("Elan linkləri tapılmadı.");
            return 0;
        }

        foreach ($links as $link) {
            sleep(5);
            $relativeLink = $link->href;
            $fullLink = $baseUrl . $relativeLink;
            $this->info("Emal edilir: {$fullLink}");

            $adHtml = $client->load($fullLink);
            if (!$adHtml) {
                $this->error("Elan səhifəsi əldə oluna bilmədi: {$fullLink}");
                continue;
            }

            $ownerDiv = $adHtml->find('div.product-owner__info-name', 0);
            if (!$ownerDiv) {
                $this->error("Elan sahibinin adı tapılmadı: {$fullLink}");
                continue;
            }
            $ownerName = trim($ownerDiv->plaintext);

            $parts = explode('/', trim($relativeLink, '/'));

            $adId = end($parts);

            if (empty($adId)) {
                $this->error("Elan ID tapılmadı: {$fullLink}");
                continue;
            }


            $apiUrl = $baseUrl . "/ads/{$adId}/phones";


            $phoneResponse = Http::post($apiUrl);
            if (!$phoneResponse->successful()) {
                $this->error("Elan ID {$adId} üçün telefon məlumatı əldə oluna bilmədi.");
                continue;
            }

            $phoneData = $phoneResponse->json();
            if (!isset($phoneData['phones']) || !is_array($phoneData['phones']) || empty($phoneData['phones'])) {
                $this->error("Elan ID {$adId} üçün telefon nömrəsi tapılmadı.");
                continue;
            }

            $phoneNumber = $this->localToInternational($phoneData['phones'][0]);


            $sentMessage = SentMessage::where('phone', $phoneNumber)->first();

            $message = "Salam, {$ownerName}

Elanınızı Tap.az platformasında gördük. Daha çox potensial müştəriyə çatmaq istəyirsiniz?

MyHome.az-da elanınızı tamamilə ödənişsiz yerləşdirərək minlərlə daşınmaz əmlak alıcısına çatdıra bilərsiniz!

Satış prosesinizi sürətləndirmək üçün elə indi MyHome.az saytına daxil olaraq elanınızı yerləşdirin.

Sualınız olarsa, bizə yaza bilərsiniz.";
            if (!$sentMessage) {
              $response  = $this->whatsappService->sendMessage(3200835958,
                  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3NDY2MTIyMTIsInVzZXJfaWQiOjI4MjR9.o0L-845aMXgbY9-cJMYU1iNlsn1FVQjGkeu0hS6a5mI',
                    $phoneNumber,
                    $message
              );

            }


        }

        return 0;
    }

    function localToInternational(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone); // Nəticə: "0704373343"

        // Əgər nömrə "0" ilə başlayırsa, bu "0" çıxarılır
        if (substr($digits, 0, 1) === '0') {
            $digits = substr($digits, 1); // Nəticə: "704373343"
        }

        // +994 ölkə kodunu əlavə edirik
        return '+994' . $digits; // Nəticə: "+994704373343"
    }


}
