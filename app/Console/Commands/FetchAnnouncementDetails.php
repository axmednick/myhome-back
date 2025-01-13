<?php

namespace App\Console\Commands;

use App\Models\DataAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use simplehtmldom\HtmlWeb;

class FetchAnnouncementDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:announcement-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches announcement details (name, role, phone) from bina.az based on links in links.json';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $htmlWeb = new HtmlWeb();

        // JSON fayldan linkləri oxuyuruq
        $filePath = storage_path('app/rents.json');
        $links = json_decode(file_get_contents($filePath), true);

        // Progress bar yaratmaq
        $totalLinks = count($links);
        $bar = $this->output->createProgressBar($totalLinks);
        $bar->start();

        // Yeni insert olunan agentlərin sayını izləyirik
        $insertedCount = 0;

        foreach ($links as $index => $link) {
            sleep(1);
            try {
                // Hər elanın HTML məlumatını çəkin
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

                        // Agent bazada yoxdursa, əlavə et və sayını artır
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

            // Progress bar-ı artırırıq və yeni insert olunan sayını göstəririk
            $bar->setMessage("New Agents Inserted: $insertedCount");
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nAnnouncement details have been saved. Total new agents inserted: {$insertedCount}");

        return Command::SUCCESS;
    }
}
