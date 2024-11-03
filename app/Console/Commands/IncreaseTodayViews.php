<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IncreaseTodayViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'increase:today-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increase view count for announcements added today at specific intervals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Yalnız bu gün əlavə olunan elanları seç
        $today = Carbon::today();
        $announcements = Announcement::whereDate('created_at', $today)->get();

        foreach ($announcements as $announcement) {
            // Elanın əlavə olunmasından neçə saat keçdiyini hesabla
            $hoursSinceCreated = Carbon::now()->diffInHours($announcement->created_at);

            // Artış miqdarını saat intervalına görə təyin et
            $increment = $this->getIncrementAmount($hoursSinceCreated);

            if ($increment > 0) {
                $announcement->increment('view_count', $increment);

                // Log (isteğe bağlı, yoxlamaq üçün)
                Log::info("Increased view count for Announcement ID {$announcement->id} by {$increment}");
            }
        }

        $this->info("Successfully increased view count for today's announcements.");
    }

    /**
     * Saat intervalına görə artış miqdarını təyin edir.
     *
     * @param int $hoursSinceCreated
     * @return int
     */
    private function getIncrementAmount(int $hoursSinceCreated)
    {
        if ($hoursSinceCreated == 0) {
            return rand(50, 100);
        } elseif ($hoursSinceCreated == 1) {
            return rand(50, 70);
        } elseif ($hoursSinceCreated == 2) {
            return rand(45, 50);
        } elseif ($hoursSinceCreated == 3) {
            return rand(40, 45);
        }

        elseif ($hoursSinceCreated == 4) {
            return rand(35, 40);
        }
        elseif ($hoursSinceCreated == 5) {
            return rand(30, 35);
        }
        elseif ($hoursSinceCreated == 6) {
            return rand(20, 30);
        }
        elseif ($hoursSinceCreated == 7) {
            return rand(10, 20);
        }
        elseif ($hoursSinceCreated == 8) {
            return rand(1, 10);
        }

        else {

            // Daha sonra artım yoxdur
            return 0;
        }
    }
}
