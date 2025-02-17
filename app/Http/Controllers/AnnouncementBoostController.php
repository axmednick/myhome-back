<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementBoost;
use App\Models\PaidServiceOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnnouncementBoostController extends Controller
{
    /**
     * Elanı irəli çək.
     */
    public function boost(Request $request): JsonResponse
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'option_id' => 'required|exists:paid_service_options,id'
        ]);

        $option = PaidServiceOption::find($request->option_id);

        if ($option->service->type !== 'forward') {
            return response()->json([
                'status' => false,
                'message' => 'Seçilmiş xidmət "Elanı İrəli Çək" xidmətinə aid deyil.'
            ], 400);
        }

        $announcement = Announcement::find($request->announcement_id);

        $boost = AnnouncementBoost::create([
            'announcement_id' => $announcement->id,
            'total_boosts' => $option->duration,
            'remaining_boosts' => $option->duration - 1,
            'last_boosted_at' => Carbon::now()
        ]);

        $announcement->update(['created_at' => Carbon::now()]);

        return response()->json([
            'status' => true,
            'message' => 'Elan uğurla irəli çəkildi.',
            'boost' => $boost
        ]);
    }
}
