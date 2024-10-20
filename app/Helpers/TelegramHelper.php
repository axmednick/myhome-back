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
        self::$botToken = '8157799965:AAFN_3mO3UNTOvoT3lRc2737CY3aM_e1-HA';
        self::$chatId = '-1002302339821';
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
