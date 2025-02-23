<?php

namespace App\Services;

use App\Helpers\DiscountCalculatorHelper;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\Announcement;
use Carbon\Carbon;

class SubscriptionService
{
    public function __construct(protected UserService $userService)
    {
    }

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
    public function subscribePackage($user, $packageId, $durationDays)
    {
        $package = Package::findOrFail($packageId);

        $finalPrice = DiscountCalculatorHelper::calculateDiscountedPrice($package->price, $durationDays);


        $this->userService->deductBalance($user, $finalPrice);


        if ($user->user_type === 'agent' && $user->agency_id) {
            return Subscription::updateOrCreate(
                ['agency_id' => $user->agency_id],
                [
                    'package_id' => $package->id,
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays($durationDays),
                    'is_active' => true,
                    'user_id' => null
                ]
            );
        }

        if ($user->user_type === 'agent' && is_null($user->agency_id)) {
            return Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'package_id' => $package->id,
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays($durationDays),
                    'is_active' => true,
                    'agency_id' => null
                ]
            );
        }

        return null;
    }



}
