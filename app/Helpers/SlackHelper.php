<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class SlackHelper
{
    public static function sendMessage($message)
    {
        $client = new Client();

        $response = $client->post('https://hooks.slack.com/services/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', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'text' => "ht is now active!",
            ],
        ]);

    }
}
