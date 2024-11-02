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

        // Əvvəlki JSON fayldan linkləri oxuyuruq
        $filePath = storage_path('app/links.json');
        $links = json_decode(file_get_contents($filePath), true);

        // Nəticə məlumatlarını saxlayacağımız fayl
        $outputFilePath = storage_path('app/announcement_details.json');

        // Faylı təmizləyərək yenidən yazmaq üçün boş bir JSON massiv ilə başlayaq
        file_put_contents($outputFilePath, "[\n");

        foreach ($links as $index => $link) {

            // Hər elanın HTML məlumatını çəkin
            $html = $htmlWeb->load($link);

            if ($html) {
                // Şəxsin adını və vəzifəsini tapırıq
                $name = $html->find('.product-owner__info-name', 0)->plaintext ?? 'N/A';
                $role = $html->find('.product-owner__info-region', 0)->plaintext ?? 'N/A';

                // Elanın ID-sini linkdən çıxarırıq
                preg_match('/items\/(\d+)/', $link, $matches);
                $announcementId = $matches[1] ?? null;

                if ($announcementId) {
                    // Əlaqə nömrəsini API-dən əldə etmək
                    $phoneApiUrl = "https://bina.az/items/{$announcementId}/phones?source_link=" . urlencode($link) . "&trigger_button=main";
                    $phoneResponse = Http::get($phoneApiUrl);

                    // Əgər API uğurlu cavab verirsə, əlaqə nömrəsini çıxarırıq
                    if ($phoneResponse->successful()) {
                        $phoneData = $phoneResponse->json();
                        $phone = $phoneData['phones'][0] ?? 'N/A';
                    } else {
                        $phone = 'N/A';
                    }

                    // Məlumatları JSON formatında fayla əlavə edirik
                    $data = [
                        'name' => $name,
                        'role' => $role,
                        'phone' => $phone,
                    ];

                    if (!DataAgent::where('phone',$phone)->first()) {
                        DataAgent::create([
                            'name' => $name,
                            'type' => $role,
                            'phone' => $phone,
                            'site' => 'bina.az'
                        ]);
                    }
                    echo $name,' '. $role,' '.$phone."\n";

                    // JSON formatında yazmaq və hər məlumatdan sonra vergül əlavə etmək
                    $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    file_put_contents($outputFilePath, $jsonData . ",\n", FILE_APPEND);
                }
            } else {
                $this->error("Failed to fetch details from: {$link}");
            }
        }

        // Faylı bağlamaq üçün son nöqtə qoyuruq və JSON formatına uyğunlaşırıq
        file_put_contents($outputFilePath, "\n]", FILE_APPEND);

        $this->info("Announcement details have been saved to announcement_details.json");

        return Command::SUCCESS;
    }
}
