<?php

namespace App\Helpers;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;


class TelegramHelper
{

    public static function sendMessage($message)
    {
        return TelegramMessage::create()->to('-1001234567890')->content('Hello, world!');
    }
}
