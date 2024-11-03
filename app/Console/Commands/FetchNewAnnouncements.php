<?php

namespace App\Console\Commands;

use App\Models\AnnouncementData; // AnnouncementData modelinin dəqiq adını yoxla
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use simplehtmldom\HtmlWeb;

class FetchNewAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:new-announcements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches new announcements from bina.az and saves them if not already in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $htmlWeb = new HtmlWeb();
        $links = [];
        $maxAnnouncements = 800;
        $page = 1;

        // Yalnız 100 elan yükləyən döngü
        while (count($links) < $maxAnnouncements) {
            $url = "https://bina.az/alqi-satqi/menziller?page={$page}";
            $this->info("Fetching page: {$page}");

            $html = $htmlWeb->load($url);

            if ($html) {
                // Hər səhifədəki elanların linklərini seçirik
                foreach ($html->find('.items-i a') as $element) {
                    $href = $element->href;
                    if ($href) {
                        $links[] = 'https://bina.az' . $href;

                        // Əgər link sayı 100-ə çatırsa, döngünü dayandırırıq
                        if (count($links) >= $maxAnnouncements) {
                            break 2;
                        }
                    }
                }
                $page++;
            } else {
                $this->error("Failed to fetch page: {$page}");
                break;
            }
        }

        // Hər bir elan linkini işləmək üçün progress bar
        $this->output->progressStart(count($links));

        foreach ($links as $link) {
            try {
                $html = $htmlWeb->load($link);

                if ($html) {
                    // Elanın ID-sini linkdən çıxarırıq
                    preg_match('/items\/(\d+)/', $link, $matches);
                    $announcementId = $matches[1] ?? null;

                    // Şəxsin adını və rolu tapırıq
                    $name = $html->find('.product-owner__info-name', 0)->plaintext ?? 'N/A';
                    $role = $html->find('.product-owner__info-region', 0)->plaintext ?? 'N/A';

                    // Əgər rol "vasitəçi (agent)"dirsə və elan artıq bazada yoxdursa, məlumatı saxlayırıq
                    if ($announcementId && $role === 'vasitəçi (agent)' && !AnnouncementData::where('announcement_id', $announcementId)->exists()) {
                        // Əlaqə nömrəsini API-dən əldə edirik
                        $phoneApiUrl = "https://bina.az/items/{$announcementId}/phones?source_link=" . urlencode($link) . "&trigger_button=main";
                        $phoneResponse = Http::get($phoneApiUrl);

                        if ($phoneResponse->successful()) {
                            $phoneData = $phoneResponse->json();
                            $phone = $phoneData['phones'][0] ?? 'N/A';

                            if ($role!='vasitəçi (agent)') {
                                AnnouncementData::create([
                                    'announcement_id' => $announcementId,
                                    'name' => $name,
                                    'role' => $role,
                                    'phone' => $phone,
                                    'site' => 'bina.az'
                                ]);
                                $this->info("Yeni elan qeyd edildi: {$name}, {$phone}");
                            }


                        } else {
                            $this->error("Əlaqə nömrəsi yüklənmədi: {$link}");
                        }
                    }
                } else {
                    $this->error("Failed to fetch details from: {$link}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing link {$link}: " . $e->getMessage());
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info("Elan məlumatları yoxlanıldı və yeni elanlar bazaya əlavə olundu.");

        return Command::SUCCESS;
    }
}
