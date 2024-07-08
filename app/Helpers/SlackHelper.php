<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

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
                'text' => "Myhome: {$message}",
            ],
        ]);

    }
}
