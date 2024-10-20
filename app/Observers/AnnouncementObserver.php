<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Mail\AnnouncementCreated;
use App\Mail\AnnouncementStatusUpdated;
use App\Mail\UserRegisteredMail;
use App\Models\Announcement;
use Illuminate\Support\Facades\Mail;

class AnnouncementObserver
{
    public function created(Announcement $announcement)
    {

        try {
            TelegramHelper::sendMessage(' created a new announcement: ' );
        } catch (\Exception $e) {
            \Log::error('Telegram mesaj göndərilmədi: ' . $e->getMessage());
        }

        try {
            Mail::to($announcement->user->email)->queue(new AnnouncementCreated($announcement));
        } catch (\Exception $e) {
            \Log::error('Mail göndərilmədi: ' . $e->getMessage());
        }

    }

    public function updated(Announcement $announcement)
    {
        if ($announcement->isDirty('status') && $announcement->status == 1) {
            Mail::to($announcement->user->email)->queue(new AnnouncementStatusUpdated($announcement));
        }
    }
}
