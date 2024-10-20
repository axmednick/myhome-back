<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class SlackHelper
{
    public static function sendMessage($message)
    {

        $slackUrl = 'https://hooks.slack.com/services/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';

        $message = [
            'text' =>$message,
        ];

        return $response = Http::post($slackUrl, $message);

    }
}
