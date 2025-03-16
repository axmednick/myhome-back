<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class RenewFreeSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:renew-free';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically renew free subscriptions (package_id = 4) when expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredSubscriptions = Subscription::where('package_id', 4)
            ->where('end_date', '<', Carbon::now())
            ->where('is_active', false) // Yalnız deaktiv olmuşları götürək
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired free subscriptions found.');
            return;
        }

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update([
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(30),
                'is_active' => true
            ]);

            $this->info("Renewed subscription for user_id: {$subscription->user_id} or agency_id: {$subscription->agency_id}");
        }

        $this->info('All expired free subscriptions have been renewed.');
    }
}
