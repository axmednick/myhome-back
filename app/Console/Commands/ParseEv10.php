<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\Ev10AnnouncementOwners;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ParseEv10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ev10';

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
        $filePath = storage_path('app/sale_apartment_ids.json');
        $listingsIds = json_decode(file_get_contents($filePath), true);
        $announcementsJson = [];

        foreach ($listingsIds as $id) {
            $listingResponse = Http::get("https://ev10.az/api/v1/postings/{$id}");

            if ($listingResponse->successful()) {
                $item = $listingResponse->json();
                $userId = $item['created_by'];

                $userResponse = Http::get("https://ev10.az/api/users/{$userId}");
                $user = $userResponse->json();

                $organization = $user['organization'] ?? null;
                $companyName = $organization['title'] ?? null;
                $companyType = $organization['type'] ?? null;

                $data = Ev10AnnouncementOwners::updateOrCreate(
                    ['email' => $user['email']],
                    [
                        'announcement_id' => $item['id'],
                        'name' => $user['name'],
                        'phone' => $user['phone_number'],
                        'email' => $user['email'],
                        'company_name' => $companyName,
                        'company_type' => $companyType,
                    ]
                );

                if (isset($user['image']) && isset($user['image']['high_quality_url'])) {
                    $data->addMediaFromUrl($user['image']['high_quality_url'])->toMediaCollection('image');
                }

                if ($organization && isset($organization['avatar_url'])) {
                    $data->addMediaFromUrl($organization['avatar_url'])->toMediaCollection('organization');
                }
            }
        }

        Storage::put('announcements.json', json_encode($announcementsJson));
    }

}
