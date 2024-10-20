<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SlackHelper
{
    public static function sendMessage($message)
    {
        // Slack Webhook URL
        $slackUrl = 'https://hooks.slack.com/services/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';

        // Mesajı JSON formatına çeviririk
        $payload = [
            'text' => $message,
        ];

        // POST sorğusu göndəririk JSON formatında
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($slackUrl, $payload);
    }
}
