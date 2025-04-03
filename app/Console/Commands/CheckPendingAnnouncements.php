<?php

namespace App\Console\Commands;
use App\Enums\AnnouncementStatus;
use Illuminate\Console\Command;
use App\Models\Announcement;
use App\Mail\PendingAnnouncementNotification;
use Illuminate\Support\Facades\Mail;

class CheckPendingAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:check-pending';
    protected $description = 'Check announcements with status 0 and send notification email';

    /**
     * The console command description.
     *
     * @var string
     */


    /**
     * Execute the console command.
     */

    public function handle()
    {
        $pendingAnnouncements = Announcement::where('status', 0)->get();

        foreach ($pendingAnnouncements as $announcement){
            $announcement->status=AnnouncementStatus::Active;
            $announcement->save();
        }

        if ($pendingAnnouncements->isNotEmpty()) {


            $this->info('Gözləmədə olan elanlarla bağlı mail göndərildi.');
        } else {
            $this->info('Gözləmədə olan elan tapılmadı.');
        }
    }
}
