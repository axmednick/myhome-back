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
