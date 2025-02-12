<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementVipPremium;
use App\Models\PaidServiceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnnouncementVipController extends Controller
{
    public function makeVipOrPremium(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'option_id' => 'required|exists:paid_service_options,id'
        ]);

        // Ödənişli xidmətin seçim məlumatlarını götür
        $option = PaidServiceOption::find($request->option_id);
        $serviceType = $option->service->type;

        if (!in_array($serviceType, ['vip', 'premium'])) {
            return response()->json([
                'status' => false,
                'message' => 'Seçilmiş xidmət VIP və ya Premium xidmət deyil.'
            ], 400);
        }

        $announcement = Announcement::find($request->announcement_id);

        $vipPremium = AnnouncementVipPremium::updateOrCreate(
            [
                'announcement_id' => $announcement->id,
                'type' => $serviceType
            ],
            [
                'expires_at' => Carbon::now()->addDays($option->duration) // Müddətə əsasən vaxt əlavə olunur
            ]
        );

        return response()->json([
            'status' => true,
            'message' => "Elan uğurla $serviceType oldu.",
            'vip_premium' => $vipPremium
        ]);
    }
}
