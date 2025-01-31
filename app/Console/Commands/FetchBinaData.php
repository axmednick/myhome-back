<?php

namespace App\Console\Commands;

use App\Models\DataAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use simplehtmldom\HtmlWeb;

class FetchBinaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bina-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches announcement links and details from bina.az for multiple categories';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $htmlWeb = new HtmlWeb();
        $links = [];
        $categories = [
            'kiraye',
            'alqi-satqi/menziller',
            'alqi-satqi/heyet-evleri',
            'alqi-satqi/ofisler',
            'alqi-satqi/torpaq',
            'alqi-satqi/obyektler'
        ];

        // Fetch links from all categories
        foreach ($categories as $category) {
            for ($page = 1; $page <= 100; $page++) {
                sleep(1);
                $url = "https://bina.az/{$category}?page={$page}";
                $this->info("Fetching: {$url}");

                $html = $htmlWeb->load($url);
                if ($html) {
                    foreach ($html->find('.items-i a') as $element) {
                        $href = $element->href;
                        if ($href) {
                            $links[] = 'https://bina.az' . $href;
                        }
                    }
                } else {
                    $this->error("Failed to fetch: {$url}");
                    break;
                }
            }
        }

        $filePath = storage_path('app/rents.json');
        file_put_contents($filePath, json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("Links saved to rents.json");

        // Fetch announcement details
        $this->fetchDetails($links, $htmlWeb, $filePath);

        return Command::SUCCESS;
    }

    private function fetchDetails(array $links, HtmlWeb $htmlWeb, string $filePath)
    {
        $totalLinks = count($links);
        $bar = $this->output->createProgressBar($totalLinks);
        $bar->start();
        $insertedCount = 0;

        foreach ($links as $index => $link) {
            sleep(1);
            try {
                $html = $htmlWeb->load($link);
                if ($html) {
                    $name = $html->find('.product-owner__info-name', 0)->plaintext ?? 'N/A';
                    $role = $html->find('.product-owner__info-region', 0)->plaintext ?? 'N/A';

                    preg_match('/items\/(\d+)/', $link, $matches);
                    $announcementId = $matches[1] ?? null;

                    if ($announcementId) {
                        $phoneApiUrl = "https://bina.az/items/{$announcementId}/phones?source_link=" . urlencode($link) . "&trigger_button=main";
                        $phoneResponse = Http::get($phoneApiUrl);

                        $phone = 'N/A';
                        if ($phoneResponse->successful()) {
                            $phoneData = $phoneResponse->json();
                            $phone = $phoneData['phones'][0] ?? 'N/A';
                        }

                        if (!DataAgent::where('phone', $phone)->exists()) {
                            DataAgent::create([
                                'name' => $name,
                                'type' => $role,
                                'phone' => $phone,
                                'site' => 'bina.az'
                            ]);
                            $insertedCount++;
                        }

                        unset($links[$index]);
                        file_put_contents($filePath, json_encode(array_values($links), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    }
                } else {
                    $this->error("Failed to fetch details from: {$link}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing link {$link}: " . $e->getMessage());
            }
            $bar->setMessage("New Agents Inserted: $insertedCount");
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nAnnouncement details saved. Total new agents inserted: {$insertedCount}");
    }
}
