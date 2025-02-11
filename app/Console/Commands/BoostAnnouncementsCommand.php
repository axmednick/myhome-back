<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AnnouncementBoost;
use Carbon\Carbon;

class BoostAnnouncementsCommand extends Command
{
    protected $signature = 'announcements:boost';
    protected $description = 'İrəli çəkilən elanları 24 saatdan bir yenilə.';

    public function handle()
    {
        $now = Carbon::now();

        $boosts = AnnouncementBoost::where('remaining_boosts', '>', 0)
            ->where('last_boosted_at', '<=', $now->subHours(24))
            ->get();

        foreach ($boosts as $boost) {
            $boost->announcement->touch();

            $boost->update([
                'remaining_boosts' => $boost->remaining_boosts - 1,
                'last_boosted_at' => $now
            ]);

            $this->info("Elan ID {$boost->announcement_id} yeniləndi.");
        }

        $this->info('Bütün uyğun elanlar yeniləndi.');
    }
}
