<?php

namespace App\Providers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Models\Announcement;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserBonus;
use App\Observers\AnnouncementObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\UserBonusObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use App\Notifications\TelegramMessageNotification;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        //Notification::send(auth('sanctum')->user(), new TelegramMessageNotification());

        Announcement::observe(AnnouncementObserver::class);
        UserBonus::observe(UserBonusObserver::class);
        User::observe(UserObserver::class);
        Subscription::observe(SubscriptionObserver::class);


    }
}
