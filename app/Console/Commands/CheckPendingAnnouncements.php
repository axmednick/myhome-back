<?php

namespace App\Console\Commands;
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

        if ($pendingAnnouncements->isNotEmpty()) {
            Mail::to(['mr.aghabayli@gmail.com', 'ahmad@rustamov.az'])
                ->queue(new PendingAnnouncementNotification($pendingAnnouncements));

            $this->info('Gözləmədə olan elanlarla bağlı mail göndərildi.');
        } else {
            $this->info('Gözləmədə olan elan tapılmadı.');
        }
    }
}