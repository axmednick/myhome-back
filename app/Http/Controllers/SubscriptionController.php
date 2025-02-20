<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Resources\SubscriptionResource;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * İstifadəçi və ya agentliyin paket məlumatlarını qaytarır
     */
    public function getSubscriptionInfo(Request $request)
    {
        $user = auth('sanctum')->user();
        $data = $this->subscriptionService->getSubscriptionDetails($user);

        return response()->json([
            'status' => true,
            'subscription' => $data['subscription'] ? SubscriptionResource::make($data['subscription']) : null,
            'package' => $data['package'] ? PackageResource::make($data['package']) : null,
            'used_listing_count' => $data['used_listing_count'],
            'remaining_listing_count' => $data['remaining_listing_count'],
            'is_active' => $data['is_active'],
            'user_type' => $user->user_type,
        ]);
    }
}
