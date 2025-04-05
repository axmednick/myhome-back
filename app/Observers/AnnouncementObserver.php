<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Mail\AnnouncementCreated;
use App\Mail\AnnouncementStatusUpdated;
use App\Mail\UserRegisteredMail;
use App\Models\Announcement;
use App\Models\Bonus;
use App\Models\UserBonus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class AnnouncementObserver
{
    public function created(Announcement $announcement)
    {
        TelegramHelper::sendMessage($announcement->user->name . ' created a new announcement: ' . $announcement->id);

        $user = $announcement->user;

    }

    public function updated(Announcement $announcement)
    {
        if ($announcement->isDirty('status') && $announcement->status == 1) {
            Mail::to($announcement->user->email)->queue(new AnnouncementStatusUpdated($announcement));
        }

        Artisan::call('optimize:clear');
    }

}
