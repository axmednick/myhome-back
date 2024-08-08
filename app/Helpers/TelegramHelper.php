<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class TelegramHelper
{
    protected static $botToken;
    protected static $chatId;
    protected static $client;

    public static function initialize()
    {
        self::$botToken = env('TELEGRAM_BOT_TOKEN');
        self::$chatId = env('TELEGRAM_CHAT_ID');
        self::$client = new Client();
    }

    public static function sendMessage($message)
    {
        if (!self::$botToken || !self::$chatId || !self::$client) {
            self::initialize();
        }

        $url = "https://api.telegram.org/bot" . self::$botToken . "/sendMessage";

        $response = self::$client->post($url, [
            'form_params' => [
                'chat_id' => self::$chatId,
                'text' => $message,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }


    public static function sendError($message)
    {
        if (!self::$botToken || !self::$chatId || !self::$client) {
            self::initialize();
        }

        $url = "https://api.telegram.org/bot" . self::$botToken . "/sendMessage";

        $response = self::$client->post($url, [
            'form_params' => [
                'chat_id' => '-1002235708455',
                'text' => $message,
            ],
        ]);


        return json_decode($response->getBody()->getContents(), true);
    }
}
