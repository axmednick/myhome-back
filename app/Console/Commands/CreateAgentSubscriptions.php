<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;

class CreateAgentSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:agent-subscriptions {package_id} {duration_days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates subscriptions for all users with user_type = agent';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $packageId = $this->argument('package_id');
        $durationDays = $this->argument('duration_days');

        // Agent tipli istifadəçiləri əldə edirik
        $agents = User::where('user_type', 'agent')->get();

        foreach ($agents as $agent) {
            Subscription::create([
                'user_id'    => $agent->id,
                'package_id' => $packageId,
                'start_date' => Carbon::now(),
                'end_date'   => Carbon::now()->addDays($durationDays),
                'is_active'  => true,
            ]);
        }

        $this->info("{$agents->count()} agent üçün subscription yaradıldı.");
        return 0;
    }
}
