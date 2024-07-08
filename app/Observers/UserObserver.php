<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Models\Announcement;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {

        //SlackHelper::sendMessage($user->name . ' Registered with: '.$user->register_type);

    }
}
