<?php

namespace App\Observers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Mail\AnnouncementCreated;
use App\Mail\AnnouncementStatusUpdated;
use App\Mail\UserRegisteredMail;
use App\Models\Announcement;
use App\Models\Bonus;
use App\Models\UserBonus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class AnnouncementObserver
{
    public function created(Announcement $announcement)
    {
        TelegramHelper::sendMessage($announcement->user->name . ' created a new announcement: ' . $announcement->id);

        $user = $announcement->user;

        \DB::transaction(function () use ($user) {
            $announcementCount = $user->announcements()->count();

            // Bonus tapırıq ki, elan sayı şərtini qarşılasın
            $bonus = Bonus::where('announcement_count', $announcementCount)->first();

            if ($bonus) {
                // İstifadəçinin bu bonusu alıb-almadığını yoxlayırıq
                $userBonusExists = UserBonus::where('user_id', $user->id)
                    ->where('bonus_id', $bonus->id)
                    ->lockForUpdate()
                    ->exists();

                if (!$userBonusExists) {
                    // Əgər bonusu almayıbsa, bonus balansına əlavə edirik və `user_bonuses` cədvəlində qeyd yaradılır
                    $user->increment('bonus_balance', $bonus->bonus_amount);

                    UserBonus::create([
                        'user_id' => $user->id,
                        'bonus_id' => $bonus->id,
                    ]);
                }
            }
        });
    }

    public function updated(Announcement $announcement)
    {
        if ($announcement->isDirty('status') && $announcement->status == 1) {
            Mail::to($announcement->user->email)->queue(new AnnouncementStatusUpdated($announcement));
        }

        Artisan::call('optimize:clear');
    }

}
