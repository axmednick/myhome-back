<?php

namespace App\Services;

use App\Models\SentMessage;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
    /**
     * WhatsApp mesajını göndərir.
     *
     * @param int|string $regId
     * @param string $token
     * @param string $phone
     * @param string $message
     * @param int $sendSpeed
     * @return array
     */
    public function sendMessage($regId, $token, $phone, $message, $sendSpeed = 1): array
    {
        $formattedPhone = $this->formatPhoneNumber($phone);

        $response = Http::asForm()->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post('https://api.wamessage.app/whatsapp/sendmessage/multi', [
            'reg_id'    => $regId,
            'contacts'  => $formattedPhone,
            'message'   => $message,
            'send_speed'=> $sendSpeed,
        ]);

        // Response-dan məlumatları çıxarıb sent_messages cədvəlinə loglayaq
        $sentMessage = new SentMessage();
        $sentMessage->phone   = $response['data']['contacts'] ?? $phone;
        $sentMessage->message = $response['data']['message'] ?? $message;
        $sentMessage->status  = $response['description'] ?? 'No status';
        $sentMessage->save();

        return $response->json();
    }

    /**
     * Telefon nömrəsinin formatını düzəldir.
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            return '+994' . substr($phone, 1);
        }

        if (substr($phone, 0, 4) === '+994') {
            return $phone;
        }

        return '+994' . $phone;
    }
}
