<?php

namespace App\Console\Commands;

use App\Models\DataAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use simplehtmldom\HtmlWeb;

class DashinmakEmlakUsersParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'd1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dashinmaz Emlak User Parser';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new HtmlWeb();
        $maxPages = 1885;
        $progressBar = $this->output->createProgressBar($maxPages);

        for ($page = 136; $page <= $maxPages; $page++) {
            $html = $client->load('https://yeniemlak.az/elan/axtar?seher%5B%5D=0&metro%5B%5D=0&qiymet=&qiymet2=&mertebe=&mertebe2=&otaq=&otaq2=&sahe_m=&sahe_m2=&sahe_s=&sahe_s2=$page'.$page);
            $announcements = $html->find('.list');

            foreach ($announcements as $announcement) {
                $announcementUrl = 'htt ps://yeniemlak.az'.$announcement->find('a', 0)->getAttribute('href');
                Log::error($announcementUrl);
                sleep(5);
                $announcementDetailPage = $client->load($announcementUrl);
                $userName = $announcementDetailPage->find('.ad', 0)->text();
                $userType = $announcementDetailPage->find('.elvrn', 0)->text();
                $userPhoneDiv = $announcementDetailPage->find('.tel', 0);
                $userPhone = str_replace('/tel-show/', '', $userPhoneDiv->find('img', 0)->getAttribute('src'));

                DataAgent::create([
                    'name'=>$userName,
                    'type'=>$userType,
                    'phone'=>$userPhone,
                    'site'=>'yeniemlak.az'
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
