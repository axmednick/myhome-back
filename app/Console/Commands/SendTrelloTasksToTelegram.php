<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TrelloHelper;
use App\Helpers\TelegramHelper;

class SendTrelloTasksToTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trello:send-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trello tasklarını Telegram kanalına göndərir';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Trello board ID-sini env faylından götürürük
        $boardId = env('TRELLO_BOARD_ID');

        // Board-dan bütün listləri çəkirik
        $lists = TrelloHelper::getListsFromBoard($boardId);

        // "Doing" listini tapırıq
        $doingList = null;
        foreach ($lists as $list) {
            if ($list['name'] === 'Doing') {
                $doingList = $list;
                break;
            }
        }

        // Əgər "Doing" listi tapılmazsa, geri qayıdırıq
        if (!$doingList) {
            $this->error('"Doing" listi tapılmadı.');
            return 1;
        }

        // "Doing" listindəki taskları götürürük
        $cards = TrelloHelper::getCardsFromList($doingList['id']);

        // Hər task üçün mesajı formatlayırıq
        foreach ($cards as $card) {
            if (!empty($card['idMembers'])) {
                foreach ($card['idMembers'] as $memberId) {
                    $memberName = TrelloHelper::getMemberName($memberId);

                    $message = "$memberName sizin aktiv taskınız var.\n";
                    $message .= "Taskın linki: " . $card['url'];

                    // Telegram-a mesaj göndəririk
                    TelegramHelper::sendMessage($message);
                }
            } else {
                $message = "Bu task təyin edilməyib: " . $card['name'] . "\n";
                $message .= "Taskın linki: " . $card['url'];

                // Telegram-a mesaj göndəririk
                TelegramHelper::sendMessage($message);
            }
        }

        $this->info('Trello taskları Telegram-a uğurla göndərildi!');
        return 0;
    }
}
