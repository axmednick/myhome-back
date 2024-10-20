<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Models\Announcement;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {

        TelegramHelper::sendMessage('New user registered '. $user->name.' Type: '.$user->register_type);


    }
}
