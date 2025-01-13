<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use simplehtmldom\HtmlWeb;
use App\Models\Listing;

class Turbo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'turbo:parse-listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse listings directly from turbo.az and insert into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = 'https://turbo.az';
        $listingsPage = '/autos';
        $htmlWeb = new HtmlWeb();

        // Əsas səhifənin HTML məzmununu yükləyirik
        $html = $htmlWeb->load($baseUrl . $listingsPage);

        if (!$html) {
            $this->error('Failed to load the main page.');
            return;
        }

        // Elan linklərini seçmək üçün HTML strukturundan istifadə
        $links = [];
        foreach ($html->find('.products a') as $element) {
            if (isset($element->href)) {
                $links[] = $baseUrl . $element->href;
            }
        }

        if (empty($links)) {
            $this->error('No listings found on the page.');
            return;
        }



        $this->info('Found ' . count($links) . ' listings. Parsing details...');

        // Hər bir linki parse edirik
        foreach ($links as $link) {
            $listingHtml = $htmlWeb->load($link);

            if (!$listingHtml) {
                $this->error("Failed to load listing page: $link");
                continue;
            }

            // Sahiblərin adını tapırıq
            $nameElement = $listingHtml->find('.product-owner__info-name', 0);
            $name = $nameElement ? trim($nameElement->plaintext) : null;

            // Telefon nömrəsini tapırıq
            $phoneElement = $listingHtml->find('.product-gallery__phone-link.is-hidden', 0);
            $phone = $phoneElement ? trim($phoneElement->href) : null;

            // Telefon nömrəsini yoxlayırıq və yeni elan əlavə edirik
            if ($name && $phone) {
                $phone = str_replace('tel:', '', $phone);

                Listing::create([
                    'name' => $name,
                    'phone' => $phone,
                    'is_agent' => Listing::where('phone', $phone)->exists(),
                ]);

                $this->info("Listing created: Name: $name, Phone: $phone");
            } else {
                $this->error("Failed to parse listing details for link: $link");
            }

            sleep(1); // Sorğular arasında fasilə
        }

        $this->info("Process completed.");
    }
}
