<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Announcement;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * İstifadəçinin və ya agentliyin aktiv abunəliyini qaytarır
     */
    public function getUserSubscription($user)
    {
        if ($user->user_type == 'user') {
            return null; // User-lərin paketi yoxdur
        }

        if ($user->managedAgency) {
            return Subscription::where('agency_id', $user->managedAgency->id)
                ->where('is_active', true)
                ->first();
        }

        if ($user->user_type === 'agent' && $user->agency_id) {
            return Subscription::where('agency_id', $user->agency_id)
                ->where('is_active', true)
                ->first();
        }

        if ($user->agency_id == null && $user->user_type === 'agent') {
            return Subscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
        }

        return null;
    }

    /**
     * İstifadəçinin və ya agentliyin elan yerləşdirmə sayını qaytarır
     */
    public function getListingCount($user)
    {
        if ($user->managedAgency) {
            return Announcement::where('agency_id', $user->managedAgency->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        }

        if ($user->user_type === 'agent' && $user->agency_id) {
            return Announcement::where('agency_id', $user->agency_id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        }

        return Announcement::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
    }

    /**
     * İstifadəçi və ya agentlik paketi və elan limitini qaytarır
     */
    public function getSubscriptionDetails($user)
    {
        $subscription = $this->getUserSubscription($user);
        $listingCount = $this->getListingCount($user);

        // Əgər user user_type = 'user' isə, o zaman aylıq limiti 2 elan edir
        $monthlyListingLimit = $user->user_type == 'user' ? 2 : ($subscription ? $subscription->package->listing_limit : 0);

        return [
            'subscription' => $subscription,
            'package' => $subscription ? $subscription->package : null,
            'used_listing_count' => $listingCount,
            'remaining_listing_count' => max($monthlyListingLimit - $listingCount, 0),
            'is_active' => $subscription ? $subscription->is_active : false
        ];
    }

    /**
     * Paketin aktiv olub olmadığını yoxlayan metod
     */
    public function isSubscriptionActive($user)
    {
        $subscription = $this->getUserSubscription($user);
        return $subscription ? $subscription->is_active : false;
    }

    /**
     * Paketin vaxtı bitdikdə avtomatik deaktiv etmək
     */
    public function deactivateExpiredSubscriptions()
    {
        Subscription::where('end_date', '<', Carbon::now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
