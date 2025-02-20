<?php

namespace App\Observers;

use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\Admin; // Admin modelini import edirik
use Illuminate\Support\Facades\Auth;

class SubscriptionObserver
{
    /**
     * Adminin ID-sini tapır (əgər varsa)
     */
    private function getAdminId()
    {
        $user = Auth::user();
        if ($user) {
            $admin = Admin::where('email', $user->email)->first();
            return $admin ? $admin->id : null;
        }
        return null;
    }

    /**
     * Yeni abunə yaradıldıqda loglamaq
     */
    public function created(Subscription $subscription)
    {
        SubscriptionLog::create([
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'agency_id' => $subscription->agency_id,
            'admin_id' => $this->getAdminId(), // Admin ID yoxlanır
            'action' => 'created',
            'changes' => json_encode($subscription->toArray())
        ]);
    }

    /**
     * Abunə yeniləndikdə loglamaq
     */
    public function updated(Subscription $subscription)
    {
        SubscriptionLog::create([
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'agency_id' => $subscription->agency_id,
            'admin_id' => $this->getAdminId(), // Admin ID yoxlanır
            'action' => 'updated',
            'changes' => json_encode($subscription->getChanges())
        ]);
    }

    /**
     * Abunə silindikdə loglamaq
     */
    public function deleted(Subscription $subscription)
    {
        SubscriptionLog::create([
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'agency_id' => $subscription->agency_id,
            'admin_id' => $this->getAdminId(), // Admin ID yoxlanır
            'action' => 'deleted',
            'changes' => json_encode($subscription->toArray())
        ]);
    }
}
