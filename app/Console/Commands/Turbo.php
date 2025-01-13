<?php

namespace App\Console\Commands;

use App\Models\ParsedAnnouncement;
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
            // Əgər link artıq parse olunubsa, keç
            if (ParsedAnnouncement::where('link', $link)->exists()) {
                $this->info("Link already parsed: $link");
                continue;
            }

            $listingHtml = $htmlWeb->load($link);

            if (!$listingHtml) {
                $this->error("Failed to load listing page: $link");
                continue;
            }

            $nameElement = $listingHtml->find('.product-owner__info-name', 0);
            $name = $nameElement ? trim($nameElement->plaintext) : null;

            $phoneElement = $listingHtml->find('.product-gallery__phone-link.is-hidden', 0);
            $phone = $phoneElement ? trim($phoneElement->href) : null;

            if ($name && $phone) {
                $phone = str_replace('tel:', '', $phone);

                $listing = Listing::firstOrCreate(
                    ['phone' => $phone],
                    ['name' => $name, 'ads_count' => 1]
                );

                // Əgər artıq varsa, `ads_count` artır
                if (!$listing->wasRecentlyCreated) {
                    $listing->increment('ads_count');
                }

                $this->info("Listing created or updated: Name: $name, Phone: $phone");

                // Parse edilmiş linki saxla
                ParsedAnnouncement::create(['link' => $link]);
            } else {
                $this->error("Failed to parse listing details for link: $link");
            }

            sleep(1);
        }

        $this->info("Process completed.");
    }
}
