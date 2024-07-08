<?php

namespace App\Providers;

use App\Helpers\SlackHelper;
use App\Models\Announcement;
use App\Models\User;
use App\Models\UserBonus;
use App\Observers\AnnouncementObserver;
use App\Observers\UserBonusObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

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

        Announcement::observe(AnnouncementObserver::class);
        UserBonus::observe(UserBonusObserver::class);
        User::observe(UserObserver::class);

    }
}
