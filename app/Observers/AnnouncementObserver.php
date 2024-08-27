<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Mail\AnnouncementCreated;
use App\Models\Announcement;
use Illuminate\Support\Facades\Mail;

class AnnouncementObserver
{
    public function created(Announcement $announcement)
    {

        TelegramHelper::sendMessage($announcement->user->name . ' created a new announcement: ' . $announcement->id);
        Mail::to($announcement->user->email)->send(new AnnouncementCreated($announcement));

    }
}
