<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class DeactivateExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:deactivate-expired';
    protected $description = 'Deactivates expired subscriptions and renews free package subscriptions';

    public function handle()
    {
        $expiredSubscriptions = Subscription::where('end_date', '<', Carbon::now())
            ->where('is_active', true)
            ->get();

        if ($expiredSubscriptions->isNotEmpty()) {
            Subscription::whereIn('id', $expiredSubscriptions->pluck('id'))
                ->update(['is_active' => false]);
            $this->info("Deactivated " . $expiredSubscriptions->count() . " expired subscriptions.");
        }

        $freeSubscriptions = Subscription::where('package_id', 4)
            ->where('end_date', '<', Carbon::now()) // Vaxtı bitmiş olmalıdır
            ->where('is_active', false) // Yalnız deaktiv olmuşları götür
            ->get();

        if ($freeSubscriptions->isNotEmpty()) {
            foreach ($freeSubscriptions as $subscription) {
                $subscription->update([
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays(30), // 30 gün əlavə edirik
                    'is_active' => true
                ]);
                $this->info("Renewed free subscription for user_id: {$subscription->user_id} or agency_id: {$subscription->agency_id}");
            }
        }

        $this->info('Expired subscriptions processed.');
    }
}
