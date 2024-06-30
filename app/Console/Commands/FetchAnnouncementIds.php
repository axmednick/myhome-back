<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchAnnouncementIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetchIds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $allIds = [];

        for ($page = 1; $page <= 50; $page++) {
            $response = Http::get("https://ev10.az/api/v1.0/postings?sale_type=PURCHASE&page_number={$page}&media_type=image&property_type=apartment&is_agent=true&sponsor_seed=1719733940083&sponsor_skip=0&sponsor_limit=12&page_size=50&search_type=home_page");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['postings']) && is_array($data['postings'])) {
                    foreach ($data['postings'] as $posting) {
                        if (isset($posting['id'])) {
                            $allIds[] = $posting['id'];
                        }
                    }
                }
            } else {
                $this->error("Failed to fetch data for page {$page}");
            }
        }

        $idsJson = json_encode($allIds);
        Storage::put('sale_apartment_ids.json', $idsJson);

        $this->info('IDs have been successfully fetched and saved.');
    }

}
