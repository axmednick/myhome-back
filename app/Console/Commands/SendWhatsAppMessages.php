<?php

namespace App\Console\Commands;

use App\Models\DataAgent;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;

class SendWhatsAppMessages extends Command
{
    protected $signature = 'send:whatsapp-messages';
    protected $description = 'Send WhatsApp messages to Vasitəçi / Rieltor contacts';

    public function handle()
    {
        // "Vasitəçi / Rieltor" olan dataları alırıq
        $contacts = DataAgent::where('type', 'Vasitəçi / Rieltor')->where('sent', false)->get();

        if ($contacts->isEmpty()) {
            $this->info('Heç bir mesaj göndərilmədi. Gözləyən kontaktlar yoxdur.');
            return;
        }

        $regId = 920119659;
        $sendSpeed = 1;
        $bearerToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MzExNDcwNzUsInVzZXJfaWQiOjI4MjR9.n7ybjQaJqWf3-gPDDbz8BbV4Fz_RIPlg3K6yH90INDg';


        foreach ($contacts as $contact) {
            $message = "Salam {$contact->name}, Sizi alqı-satqı elanlarınızı MyHome.az saytında yerləşdirməyə dəvət edirik.\n\n".
                "Yeni və funksionallığı ilə fərqlənən layihənin bu mərhələsində aktivlik göstərən rieltorlara sonrakı mərhələlərdə xüsusi imkanlar və imtiyazlar təqdim olunacaq.\n\n".
                "Elə indi qeydiyyatdan keçin, 20 elan yerləşdirin və 100₼ bonus balansı əldə edin! Bonus balansını yaxın zamanda aktivləşəcək ödənişli funksiyalar üçün istifadə edə biləcəksiniz. Qeyd edək ki, 1 ay ərzində istənilən qədər ödənişsiz elan yerləşdirmək mümkündür.\n\n".
                "Hörmətlə, MyHome.az administrasiyası";
            $formattedPhone = $this->formatPhoneNumber($contact->phone);


           // Mesajı göndəririk
           $response = Http::asForm()->withHeaders([
                'Authorization' => "Bearer {$bearerToken}"
            ])->post('https://api.wamessage.app/whatsapp/sendmessage/multi', [
                'reg_id' => $regId,
                'contacts' => $formattedPhone,
                'message' => $message,
                'send_speed' => $sendSpeed
            ]);
            $contact->wp_response=$response->json();
            $contact->save();

            if ($response->successful() && $response->json('description') == 'Mesaj gönderildi') {
                $contact->update(['sent' => true]);
                $this->info("Mesaj {$contact->phone} nömrəsinə göndərildi.");
            } else {
                $this->error("Mesaj göndərilmədi: {$contact->phone}");
            }
            sleep(10);
        }
    }
    private function formatPhoneNumber($phone)
    {
        // Əgər nömrə 0 ilə başlayırsa, onu sil və +994 əlavə et
        if (substr($phone, 0, 1) === '0') {
            return '+994' . substr($phone, 1);
        }
        return $phone;
    }
}