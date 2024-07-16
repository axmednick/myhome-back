<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Models\Announcement;

class AnnouncementObserver
{
    public function created(Announcement $announcement)
    {

        TelegramHelper::sendMessage($announcement->user->name . ' created a new announcement: ' . $announcement->id);
    }
}
