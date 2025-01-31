<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use simplehtmldom\HtmlWeb;

class FetchBinaLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:bina-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches announcement links from bina.az for multiple pages and stores them in an array';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $htmlWeb = new HtmlWeb();
        $links = [];

        for ($page = 1; $page <= 100; $page++) {
            sleep(1);
            $url = "https://bina.az/kiraye?page={$page}";
            $this->info("Fetching page: {$page}");

            $html = $htmlWeb->load($url);

            if ($html) {
                foreach ($html->find('.items-i a') as $element) {
                    $href = $element->href;
                    if ($href) {
                        $links[] = 'https://bina.az' . $href;
                    }
                }
            } else {
                $this->error("Failed to fetch page: {$page}");
                break;
            }
        }

        $filePath = storage_path('app/rents.json'); // Faylın saxlanacağı yol
        file_put_contents($filePath, json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Links have been saved to links.json");

        return Command::SUCCESS;
    }
}
