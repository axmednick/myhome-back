<?php

namespace App\Services;

use App\Enums\AnnouncementStatus;
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
        $announcementIds = null;




        $announcements = Announcement::query()

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


        if (!$request->has('city_id') || !$request->city_id) {
            $announcements->whereHas('address', function ($query) {
                $query->where('city_id', 1);
            });
        }


        if ($request->has('rental_type') && $request->rental_type) {
            $announcements->where('rental_type', $request->rental_type);
        }

        if ($request->has('agency_id')) {
            $announcements->where('agency_id', $request -> agency_id);
        }

        if ($request->has('is_premium') && $request->is_premium) {
            $announcements->where('is_premium', 1);
        }
        if ($request->has('is_vip') && $request->is_vip) {
            $announcements->where('is_vip', 1);
        }

        if ($request->client_types_for_rent) {

            if ($request->client_types_for_rent!=1) {

                Log::error($request->client_types_for_rent);
                $announcements->whereHas('rental_client_types', function ($query) use ($request) {
                    $query->where('client_type_for_rent_id', $request->client_types_for_rent);
                });
            }
        }


        if ($request->propertyType) {
            $announcements->where('property_type_id', $request->propertyType);
        }
        if ($request->announcementType) {
            $announcements->where('announcement_type_id', $request->announcementType);
        }
        if ($request->has('room_ids')) {
            $roomIds = is_array($request->query('room_ids')) ? $request->query('room_ids') : [$request->query('room_ids')];
            $announcements->where(function ($query) use ($roomIds) {
                if (in_array(5, $roomIds)) {
                    $query->where('room_count', '>=', 5);
                } else {
                    $query->whereIn('room_count', $roomIds);
                }
            });
        }


        if ($request->apartmentType) {
            $announcements->where('apartment_type_id', $request->apartmentType);
        }
        if ($request->query('minPrice')) {
            $announcements->whereRaw('CAST(price AS UNSIGNED) >= ?', [(int) $request->query('minPrice')]);
        }
        if ($request->query('maxPrice')) {
            $announcements->whereRaw('CAST(price AS UNSIGNED) <= ?', [(int) $request->query('maxPrice')]);
        }

        if ($request->minArea) {
            $announcements->where('house_area', '>=', $request->minArea);
        }
        if ($request->maxArea) {
            $announcements->where('house_area', '<=', $request->maxArea);
        }


        if ($request->search) {
            $announcements->where(function ($query) use ($request) {
                $query->where('id', $request->search)
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->user_type) {
            $announcements->whereHas('user', function ($query) use ($request) {
                $query->where('user_type', $request->user_type);
            });
        }

        if ($request->maxFloor) {
            $announcements->where('floor', '<=', (int) $request->maxFloor);
        }
        if ($request->minFloor) {
            $announcements->where('floor', '>=', (int) $request->minFloor);
        }


        if ($request->only_last) {
            $announcements->whereColumn('floor', '=', 'floor_count');
        }
        if ($request->dont_be_first) {
            $announcements->where('floor', '>', 1);
        }
        if ($request->dont_be_last) {
            $announcements->whereColumn('floor', '<', 'floor_count');
        }


        if ($request->is_repaired) {
            $announcements->where('is_repaired', $request->is_repaired);
        }
        if ($request->property_document) {
            $announcements->where('document_id', 1);
        }
        if ($request->credit_possible) {
            $announcements->where('credit_possible', 1);
        }
        if ($request->in_credit) {
            $announcements->where('in_credit', 1);
        }
        if ($request->looking_roommate) {
            $announcements->where('looking_roommate', 1);
        }

        if ($request->has('city')) {
            $announcements->whereHas('address', function ($query) use ($request) {
                $query->where('city_id', $request->city);
            });
        }
        if ($request->has('villages')) {

            $villages = is_array($request->query('villages')) ? $request->query('villages') : [$request->query('villages')];

            $announcements->whereHas('address',function ($query) use ($villages) {

                $query->whereIn('village_id', $villages);
            });
        }

        if ($request->has('metro')) {
            $metroStations = is_array($request->query('metro')) ? $request->query('metro') : [$request->query('metro')];

            $announcements->whereHas('metro_stations', function ($query) use ($metroStations) {
                $query->whereIn('metro_station_id',$metroStations);
            });
        }





        return $announcements->orderByRaw('is_premium DESC, is_vip DESC, created_at DESC');
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

