<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class TrelloHelper
{
    /**
     * Trello board-dan listləri götürmək üçün metod.
     *
     * @param string $boardId - Trello board ID
     * @return array - Listlər
     */
    public static function getListsFromBoard($boardId)
    {
        // API key və tokeni .env-dən götürürük
        $apiKey = env('TRELLO_API_KEY');
        $apiToken = env('TRELLO_API_TOKEN');

        // Trello API URL (board-dakı listləri gətirir)
        $url = "https://api.trello.com/1/boards/$boardId/lists?key=$apiKey&token=$apiToken";

        // Guzzle client ilə GET sorğusu göndəririk
        $client = new Client();
        $response = $client->get($url);

        // Cavabı JSON formatında alırıq
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

        return [];
    }

    /**
     * Trello listdən kartları (taskları) götürmək üçün metod.
     *
     * @param string $listId - Trello list ID
     * @return array - Kartlar
     */
    public static function getCardsFromList($listId)
    {
        $apiKey = env('TRELLO_API_KEY');
        $apiToken = env('TRELLO_API_TOKEN');

        // Trello API URL (listdəki kartları gətirir)
        $url = "https://api.trello.com/1/lists/$listId/cards?key=$apiKey&token=$apiToken";

        // Guzzle client ilə GET sorğusu göndəririk
        $client = new Client();
        $response = $client->get($url);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

        return [];
    }
    public static function getMemberName($memberId)
    {
        $apiKey = env('TRELLO_API_KEY');
        $apiToken = env('TRELLO_API_TOKEN');

        // Üzv məlumatını almaq üçün URL
        $url = "https://api.trello.com/1/members/$memberId?key=$apiKey&token=$apiToken";

        $client = new Client();
        $response = $client->get($url);

        if ($response->getStatusCode() == 200) {
            $member = json_decode($response->getBody(), true);
            return $member['fullName'];
        }

        return null;
    }
}
