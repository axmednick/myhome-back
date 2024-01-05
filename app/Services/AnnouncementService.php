<?php

namespace App\Services;
use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;


class AnnouncementService

{

    public function searchAnnouncements($request): Builder
    {
        $announcements = Announcement::query();

        if ($request->propertyType) {
            $announcements->where('property_type_id', $request->propertyType);
        }


        if ($request->announcementType){
            $announcements->where('announcement_type_id', $request->announcementType);

        }

        if ($request->roomCountMin){

            $announcements->where('room_count','>=', $request->roomCountMin);

        }
        if ($request->roomCountMax){
            $announcements->where('room_count','<=', $request->roomCountMax);

        }

        if ($request->apartmentType){
            $announcements->where('apartment_type_id', $request->apartmentType);

        }

        if ($request->minPrice){
            $announcements->where('price','>=', $request->minPrice);

        }

        if ($request->maxPrice){
            $announcements->where('price','<=', $request->maxPrice);

        }

        if ($request->minArea){
            $announcements->where('house_area','>=', $request->minArea);

        }

        if ($request->maxArea){
            $announcements->where('house_area','<=', $request->maxArea);

        }

        if ($request->city){
            $announcements->whereHas('address',function ($query) use($request){
                $query->where('city_id',$request->city);
            });
        }

        if ($request->regions){
            $announcements->whereHas('address',function ($query) use($request){
                $query->whereIn('region_id',$request->regions);
            });
        }
        if ($request->villages){
            $announcements->whereHas('address',function ($query) use($request){
                $query->whereIn('village_id',$request->villages);
            });
        }

        return $announcements;
    }


    public function announcementsByUser($userId){

        $announcements = Announcement::where('user_id',$userId);

        return $announcements;
    }

}

