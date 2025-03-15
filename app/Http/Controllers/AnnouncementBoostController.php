<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementBoost;
use App\Models\PaidServiceOption;
use App\Services\PaymentService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnnouncementBoostController extends Controller
{

    public function __construct(protected UserService $userService,protected PaymentService $paymentService)
    {
    }

    /**
     * Elanı irəli çək.
     */
    public function boost(Request $request): JsonResponse
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'option_id' => 'required|exists:paid_service_options,id'
        ]);

        $user = auth('sanctum')->user();
        $option = PaidServiceOption::find($request->option_id);
        $announcement = Announcement::find($request->announcement_id);

        if ($option->service->type !== 'forward') {
            return response()->json([
                'status' => false,
                'message' => 'Seçilmiş xidmət "Elanı İrəli Çək" xidmətinə aid deyil.'
            ], 400);
        }

        // Balansı yoxlayırıq
        $hasBalance = $this->userService->deductBalance($option->price);

        if (!$hasBalance) {
            // Balans yetərsiz olduqda ödəniş üçün order yaradılır
            $paymentUrl = $this->paymentService->createOrder(
                $user,
                $option->price,
                'https://api.myhome.az/api/payment/callback?announcement_id=' . $announcement->id&'option_id=' . $option->id&'type=boost',
                'https://myhome.az/panel/balans?payment=cancel',
                'https://myhome.az/panel/balans?payment=error',
                'Elan Boost xidməti'
            );


            return response()->json([
                'status' => false,
                'message' => 'Balans yetərsizdir, ödəniş tələb olunur.',
                'paymentUrl' => $paymentUrl
            ],400);
        }

        // Əgər balans yetərlidirsə, boost-u icra edirik
        $boost = AnnouncementBoost::create([
            'announcement_id' => $announcement->id,
            'total_boosts' => $option->duration,
            'remaining_boosts' => $option->duration - 1,
            'last_boosted_at' => now()
        ]);

        $announcement->update(['created_at' => now()]);

        return response()->json([
            'status' => true,
            'message' => 'Elan uğurla irəli çəkildi.',
            'boost' => $boost
        ]);
    }

}
