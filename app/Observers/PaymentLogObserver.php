<?php

namespace App\Observers;

use GuzzleHttp\Client;
use App\Models\PaymentLog;

class PaymentLogObserver
{
    // Observer daxilində dəyişə biləcəyiniz parametrler:
    protected $botToken = '8162227522:AAHbOAA4fRlQwF59kcCj1j_PMij0RSdTOnY';
    protected $chatId   = '-1002405025663';
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Transaction modelində update əməliyyatı baş verdikdə Telegram-a mesaj göndərir.
     *

     */
    public function created(PaymentLog $paymentLog)
    {
        $message = "Yeni ödəniş qeydə alındı: {$paymentLog->user->name} {$paymentLog->amount} AZN";
        $this->sendTelegramMessage($message);
    }

    /**
     * Telegram-a mesaj göndərən metod.
     *
     * @param string $message
     * @return array
     */
    protected function sendTelegramMessage($message)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        try {
            $response = $this->client->post($url, [
                'form_params' => [
                    'chat_id' => $this->chatId,
                    'text'    => $message,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Xəta baş verərsə, log yazıla bilər və ya xəta mesajı qaytarıla bilər
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
