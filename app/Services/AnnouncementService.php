<?php

namespace App\Services;

use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\AnnouncementBoost;
use App\Models\AnnouncementVipPremium;
use App\Models\Link;
use App\Models\PaidServiceOption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class AnnouncementService

{

    public function searchAnnouncements($request): Builder
    {
        $announcements = Announcement::query()->where('status',1)

            ->with([
                'address' => function ($query) use ($request) {
                    if ($request->cities) {
                        $query->whereIn('city_id', $request->cities);
                    }
                    if ($request->regions) {
                        $query->whereIn('region_id', $request->regions);
                    }
                    if ($request->villages) {
                        $query->whereIn('village_id', $request->villages);
                    }
                },
                'user'
            ]);



        if ($request->maxFloor) {
            $announcements->where('floor', '<=', (int) $request->maxFloor);
        }
        if ($request->minFloor) {
            $announcements->where('floor', '>=', (int) $request->minFloor);
        }




        return $announcements;
    }



    public function announcementsByUser($userId):Announcement
    {

        $announcements = Announcement::where('user_id', $userId)->query();

        return $announcements;
    }

    public function announcementById($id,$type=null):Announcement
    {
        $announcement = Announcement::findOrFail($id);
        if ($type=='details'){
            $announcement->view_count++;
            $announcement->save();
        }

        return $announcement;
    }

    public function similarAnnouncements($id)
    {
        $announcement = $this->announcementById($id);

        if (!$announcement) {
            return response()->json(['message' => 'Announcement not found'], 404);
        }

        $similarAnnouncements = Announcement::where('announcement_type_id', $announcement->announcement_type_id)
            ->where('property_type_id', $announcement->property_type_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')

            ->take(5)
            ->get();

        if ($similarAnnouncements->count() < 5) {
            $announcementTypeMatches = Announcement::where('announcement_type_id', $announcement->announcement_type_id)
                ->where('id', '!=', $id)
                ->orderBy('id','desc')
                ->whereNotIn('id', $similarAnnouncements->pluck('id'))
                ->take(5 - $similarAnnouncements->count())
                ->get();

            $similarAnnouncements = $similarAnnouncements->merge($announcementTypeMatches);
        }

        if ($similarAnnouncements->count() < 5) {
            $propertyTypeMatches = Announcement::where('property_type_id', $announcement->property_type_id)
                ->where('id', '!=', $id)
                ->orderBy('id','desc')
                ->whereNotIn('id', $similarAnnouncements->pluck('id'))
                ->take(5 - $similarAnnouncements->count())
                ->get();

            $similarAnnouncements = $similarAnnouncements->merge($propertyTypeMatches);
        }

        if ($similarAnnouncements->count() < 5) {
            $randomAnnouncements = Announcement::where('id', '!=', $id)
                ->orderBy('id','desc')
                ->whereNotIn('id', $similarAnnouncements->pluck('id'))
                ->inRandomOrder()
                ->take(5 - $similarAnnouncements->count())
                ->get();

            $similarAnnouncements = $similarAnnouncements->merge($randomAnnouncements);
        }

        return $similarAnnouncements;
    }

    public function announcementsByLink($link)
    {
        $link = Link::where('link', $link)->first();

        $announcementIds = json_decode($link->announcement_ids, true);

        return Announcement::whereIn('id', $announcementIds)->paginate(20);
    }

    public function boostAnnouncement($announcementId, $optionId, $user)
    {
        $announcement = Announcement::findOrFail($announcementId);
        $option = PaidServiceOption::findOrFail($optionId);


        $boost = AnnouncementBoost::create([
            'announcement_id' => $announcement->id,
            'total_boosts' => $option->duration,
            'remaining_boosts' => $option->duration - 1,
            'last_boosted_at' => now()
        ]);

        $announcement->update(['created_at' => now()]);

        return $boost;
    }
    public function makeVipOrPremiumAnnouncement($announcementId, $optionId, $user)
    {
        $announcement = Announcement::findOrFail($announcementId);
        $option = PaidServiceOption::findOrFail($optionId);
        $serviceType = $option->service->type;

        if (!in_array($serviceType, ['vip', 'premium'])) {
            return null; // Seçilmiş xidmət doğru növdə deyil
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

        return $vipPremium;
    }


}

