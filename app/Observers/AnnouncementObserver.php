<?php

namespace App\Observers;

use App\Models\Announcement;

class AnnouncementObserver
{
    public function created(Announcement $announcement)
    {
        $user = $announcement->user;

    }
}
