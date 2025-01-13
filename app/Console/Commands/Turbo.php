<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
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
    protected $description = 'Parse listings from saved links and insert into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client();
        $linksFile = storage_path('app/links.json');

        // Mövcud JSON faylını yükləyirik
        if (!File::exists($linksFile)) {
            $this->error("Links file not found.");
            return;
        }

        $linksData = json_decode(File::get($linksFile), true);
        $links = $linksData['links'] ?? [];

        if (empty($links)) {
            $this->error("No links available in the file.");
            return;
        }

        foreach ($links as $link) {
        
            $response = $client->get($link);
            $html = $response->getBody()->getContents();

            // İstifadəçi adı üçün regex
            preg_match('/<div class=\"product-owner__info-name\">(.*?)<\/div>/', $html, $nameMatch);
            $name = $nameMatch[1] ?? null;

            // Telefon nömrəsi üçün regex
            preg_match('/<a class=\"product-gallery__phone-link is-hidden\" href=\"tel:(.*?)\">/', $html, $phoneMatch);
            $phone = $phoneMatch[1] ?? null;

            if ($name && $phone) {
                // Yeni listing yaradılır
                Listing::create([
                    'name' => $name,
                    'phone' => $phone,
                    'is_agent' => Listing::where('phone', $phone)->exists(), // Mövcud telefon nömrəsini yoxlayırıq
                ]);

                $this->info("Listing created: Name: $name, Phone: $phone");
            } else {
                $this->error("Failed to parse listing for link: $link");
            }

            sleep(1); // Sorğular arasında kiçik fasilə
        }

        $this->info("Process completed.");
    }
}
