<?php

namespace App\Http\Controllers\Announcement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\AnnouncementRequest;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AnnouncementController extends Controller
{
    public function store(AnnouncementRequest $request)
    {



        if (!auth()->check()) {
            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(8))
            ]);
        } else {

            $user = auth()->user();
        }


        $announcement = Announcement::create([
            'announcement_type_id' => $request->announcement_type,
            'property_type_id' => $request->property_type,
            'apartment_type_id' => $request->apartment_type,
            'area' => $request->area ? $request->area : null,
            'house_area' => $request->house_area,
            'room_count' => $request->room_count ? $request->room_count : null,
            'floor' => $request->floor ? $request->floor : null,
            'floor_count' => $request->floor_count ? $request->floor_count : null,
            'description' => $request->description,
            'rental_type' => $request->rental_type,
            'user_id' => $user->id,
            'price' => $request->price,
            'is_repaired' => $request->is_repaired,
            'document_id'=>$request->document
        ]);




        $announcement->address()->create([
            'announcement_id' => $announcement->id,
            'city_id' => $request->city,
            'region_id' => $request->region,
            'village_id' => $request->village,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address'=>$request->address
        ]);


        foreach ($request->client_types_for_rent as $client_type_for_rent) {

            $announcement->rentalClientTypes()->create([
                'client_type_for_rent_id' =>     $client_type_for_rent
            ]);

        }


        if ($request->has('metroStations')) {

            foreach ($request->metroStations as $metroStation) {
                $announcement->metroStations()->create([
                    'metro_station_id' => $metroStation
                ]);
            }

        }


        $media = Media::whereIn('model_id', $request->media_ids)->update([
            'model_type' => 'App\Models\Announcement',
            'model_id' => $announcement->id
        ]);

        return response()->json([
            'status' => 'success'
        ]);


    }
}
