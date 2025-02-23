<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Resources\SubscriptionResource;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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


    public function subscribePackage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'package_id' => 'required|exists:packages,id',
            'duration_days' => 'required|integer|in:30,90,180,365',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }


        $user = auth('sanctum')->user();
        $subscription = $this->subscriptionService->subscribePackage($user, $request->package_id, $request->duration_days);

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'Regular users cannot purchase a subscription.',
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'Subscription successfully purchased or updated.',
            'subscription' => new SubscriptionResource($subscription),
        ]);
    }

}
