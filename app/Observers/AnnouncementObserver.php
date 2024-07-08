<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Models\Announcement;

class AnnouncementObserver
{
    public function created(Announcement $announcement)
    {

        SlackHelper::sendMessage($announcement->user()->name . ' New announcement created');

    }
}
