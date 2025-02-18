<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Announcement;
use App\Models\AnnouncementVipPremium;
use Carbon\Carbon;

class ExpireVipPremiumAnnouncements extends Command
{
    protected $signature = 'announcements:expire-vip-premium';
    protected $description = 'Expires VIP and Premium announcements if their duration is over';

    public function handle()
    {
        $now = Carbon::now();


        $expiredVipPremiums = AnnouncementVipPremium::where('is_active',true)->where('expires_at', '<', $now)->get();

        foreach ($expiredVipPremiums as $vipPremium) {
            $announcement = $vipPremium->announcement;

            if ($vipPremium->type === 'vip') {
                $announcement->update([
                    'is_vip' => false
                ]);
            } elseif ($vipPremium->type === 'premium') {
                $announcement->update([
                    'is_vip' => false,
                    'is_premium' => false
                ]);
            }


            $vipPremium->is_active=false;
            $vipPremium->save();
        }

        $this->info('Expired VIP and Premium announcements have been updated.');
    }
}
