<?php

namespace App\Providers;

use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Models\Announcement;
use App\Models\PaymentLog;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserBonus;
use App\Observers\AnnouncementObserver;
use App\Observers\PaymentLogObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\UserBonusObserver;
use App\Observers\UserObserver;
use Elastic\Elasticsearch\ClientBuilder;
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
        $this->app->singleton(\Elastic\Elasticsearch\Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([config('scout.elasticsearch.hosts')[0]])
                ->build();
        });

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
        PaymentLog::observe(PaymentLogObserver::class);


    }
}
