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


        if ($request->announcementType) {
            $announcements->where('announcement_type_id', $request->announcementType);

        }

        if ($request->room_ids) {

            if ($request->room_ids == "more") {

                $announcements->where('room_count', '>', 5);

            } else {

                $announcements->whereIn('room_count', $request->room_ids);
            }
        }

        if ($request->apartmentType) {
            $announcements->where('apartment_type_id', $request->apartmentType);

        }

        if ($request->minPrice) {
            $announcements->where('price', '>=', $request->minPrice);

        }

        if ($request->maxPrice) {
            $announcements->where('price', '<=', $request->maxPrice);

        }

        if ($request->minArea) {
            $announcements->where('house_area', '>=', $request->minArea);

        }

        if ($request->maxArea) {
            $announcements->where('house_area', '<=', $request->maxArea);

        }

        if ($request->cities) {
            $announcements->whereHas('address', function ($query) use ($request) {
                $query->whereIn('city_id', $request->cities);
            });
        }

        if ($request->regions) {
            $announcements->whereHas('address', function ($query) use ($request) {
                $query->whereIn('region_id', $request->regions);
            });
        }
        if ($request->villages) {
            $announcements->whereHas('address', function ($query) use ($request) {
                $query->whereIn('village_id', $request->villages);
            });
        }

        if ($request->metro_stations) {
            $announcements->whereHas('address', function ($query) use ($request) {
                $query->whereIn('metro_station_id', $request->metro_stations);
            });
        }

        if ($request->keyword) {
            $announcements->
            where('id', $request->keyword)->
            orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        if ($request->user_type){
            $announcements->whereHas('user',function ($query) use ($request){
               $query->where('user_type',$request->user_type);
            });
        }

        if ($request->maxFloor) {
            $announcements->where('floor', '<=', $request->maxFloor);

        }

        if ($request->minFloor) {
            $announcements->where('floor', '>=', $request->minFloor);

        }
        if ($request->is_repaired) {
            $announcements->where('is_repaired',  $request->is_repaired);

        }

        if ($request->property_document){
            $announcements->where('document_id',1);
        }
        if ($request->credit_possible){
            $announcements->where('credit_possible',1);
        }

        if ($request->in_credit){
            $announcements->where('in_credit',1);
        }

        if ($request->looking_roommate){
            $announcements->where('looking_roommate',1);
        }

        if ($request->rental_client_types){
            $announcements->whereHas('rental_client_types',function ($query) use ($request){
                $query->whereIn('client_type_for_rent_id',$request->rental_client_types);
            });
        }


        return $announcements;
    }


    public function announcementsByUser($userId)
    {

        $announcements = Announcement::where('user_id', $userId)->query();

        return $announcements;
    }

}

