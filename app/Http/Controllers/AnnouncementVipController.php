<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementVipPremium;
use App\Models\PaidServiceOption;
use App\Services\UserService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;

class AnnouncementVipController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected PaymentService $paymentService
    ) {}

    public function makeVipOrPremium(Request $request): JsonResponse
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'option_id' => 'required|exists:paid_service_options,id'
        ]);

        $user = auth('sanctum')->user();
        $option = PaidServiceOption::find($request->option_id);
        $announcement = Announcement::find($request->announcement_id);
        $serviceType = $option->service->type;

        if (!in_array($serviceType, ['vip', 'premium'])) {
            return response()->json([
                'status' => false,
                'message' => 'Seçilmiş xidmət VIP və ya Premium xidmət deyil.'
            ], 400);
        }

        $hasBalance = $this->userService->deductBalance($option->price);

        if (!$hasBalance) {
            $paymentUrl = $this->paymentService->createOrder(
                $user,
                $option->price,
                'https://api.myhome.az/api/payment/callback?announcement_id=' . $announcement->id . '&option_id=' . $option->id . '&type=' . $serviceType,
                'https://myhome.az/panel/balans?payment=cancel',
                'https://myhome.az/panel/balans?payment=error',
                'Elan ' . ucfirst($serviceType) . ' xidməti'
            );

            return response()->json([
                'status' => false,
                'message' => 'Balans yetərsizdir, ödəniş tələb olunur.',
                'paymentUrl' => $paymentUrl
            ]);
        }

        $vipPremium = AnnouncementVipPremium::updateOrCreate(
            [
                'announcement_id' => $announcement->id,
                'type' => $serviceType
            ],
            [
                'expires_at' => Carbon::now()->addDays($option->duration)
            ]
        );

        if ($serviceType === 'vip') {
            $announcement->update([
                'is_vip' => true
            ]);
        } elseif ($serviceType === 'premium') {
            $announcement->update([
                'is_vip' => true,
                'is_premium' => true
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => "Elan uğurla $serviceType oldu.",
            'vip_premium' => $vipPremium
        ]);
    }
}
