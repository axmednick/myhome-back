<?php

namespace App\Console\Commands;

use App\Services\WhatsappService;
use Illuminate\Console\Command;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetActiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:active-announcements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get users who posted more than 10 announcements in January 2024';


    public function __construct(protected WhatsappService $whatsappService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $users = DB::table('users')
            ->whereNotNull('phone')
            ->join('announcements', 'users.id', '=', 'announcements.user_id')
            ->whereBetween('announcements.created_at', ['2024-01-01 00:00:00', '2025-01-31 23:59:59'])
            ->select('users.id', 'users.name', DB::raw('COUNT(announcements.id) as announcement_count'))
            ->groupBy('users.id', 'users.name')
            ->having('announcement_count', '>', 10)
            ->get();



        foreach ($users as $user) {
            $userId = $user->id;
            $announcementCount = $user->announcement_count;
            $dbUser = User::find($userId);

            $dbUser->bonus_balance -= 200;
            $dbUser->save();

            $message = "Salam, {$dbUser->name}

MyHome.az-da {$announcementCount} elan yerləşdirdiyiniz üçün təşəkkür edirik!

Bu müddət ərzində platformamızdan istifadə etdiyiniz və inkişafına töhfə verdiyiniz üçün balansınıza 100 AZN bonus əlavə edirik. Hesabınıza daxil olaraq bu məbləğlə elanlarınız üçün “İrəli çək”, “VIP et” və “Premium et” xidmətlərindən istifadə edə bilərsiniz.

Hər hansı çətinliyiniz olarsa, bizə mesajla bildirməyinizi xahiş edirik.";

            $this->whatsappService->sendMessage(921372965,
                'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3NDQwMzMyMzksInVzZXJfaWQiOjI4MjR9.DZ2-w3eaku_CB9pC7O2PwSx4g3uScroDyv0vYouZw-I',
                $dbUser->phone,
                $message);

            echo $userId;

        }



    }
}
