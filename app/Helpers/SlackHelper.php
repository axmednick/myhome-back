<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SlackHelper
{
    public static function sendMessage($message)
    {
        $webhookUrl = 'https://hooks.slack.com/services/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';

        $response = Http::post($webhookUrl, [
            'text' => 'Myhome: '.$message
        ]);

        return $response->successful();
    }
}
