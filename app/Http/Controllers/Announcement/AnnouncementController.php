<?php

namespace App\Http\Controllers\Announcement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\MetrostationsResource;
use App\Http\Resources\SuppliesResource;
use App\Models\Announcement;
use App\Models\MetroStation;
use App\Models\Supply;
use App\Models\User;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AnnouncementController extends Controller
{
    protected $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function store(AnnouncementRequest $request)
    {
        if (!auth('sanctum')->check()) {
            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);
        } else {

            $user = auth('sanctum')->user();
        }


        $announcement = Announcement::create([
            'announcement_type_id' => $request->announcement_type,
            'property_type_id' => $request->property_type,
            'apartment_type_id' => $request->apartment_type,
            'area' => $request->area ? $request->area : null,
            'house_area' => $request->house_area,
            'room_count' => $request->roomCount ? $request->roomCount : null,
            'floor' => $request->floor ? $request->floor : null,
            'floor_count' => $request->floor_count ? $request->floor_count : null,
            'description' => $request->description,
            'user_id' => $user->id,
            'price' => $request->price,
            'is_repaired' => $request->is_repair,
            'document_id' => $request->property_document,
            'rental_type' => $request->rental_type,
        ]);


        $announcement->address()->create([
            'announcement_id' => $announcement->id,
            'city_id' => $request->city,
            'region_id' => $request->region,
            'village_id' => $request->village,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address' => $request->address,
            'credit_possible'=>$request->credit_possible,
            'in_credit'=>$request->in_credit,
        ]);


        foreach ($request->client_types_for_rent as $client_type_for_rent) {



            $announcement->rental_client_types()->create([
                'client_type_for_rent_id' => $client_type_for_rent
            ]);

        }

        if ($request->has('supplies')){
            foreach ($request->get('supplies') as $supply){
                $announcement->supplies()->create([
                    'supply_id'=>$supply
                ]);
            }
        }


        if ($request->has('metroStations')) {

            foreach ($request->metroStations as $metroStation) {
                $announcement->metro_stations()->create([
                    'metro_station_id' => $metroStation
                ]);
            }

        }

        foreach ($request->media_ids as $media) {

            $media = Media::where('model_id', $media['id'])->update([
                'model_type' => 'App\Models\Announcement',
                'model_id' => $announcement->id
            ]);
        }


        return response()->json([
            'status' => 'success'
        ]);


    }


    public function announcements(Request $request)
    {

        $announcements = Announcement::query();

        $announcements = $this->announcementService->searchAnnouncements($request);


        return AnnouncementResource::collection($announcements->orderBy('id', 'desc')->paginate(12));

    }


    public function userAnnouncements($id = null)
    {


        $announcements = $this->announcementService->announcementsByUser($id ? $id : auth('sanctum')->id());

        return AnnouncementResource::collection($announcements->paginate(12));
    }


    public function detail($id)
    {

        $announcement = Announcement::findOrFail($id);

        return AnnouncementResource::make($announcement);
    }

    public function nearbyMetroStations()
    {
        $metroStations = MetroStation::limit(3)->get();
        return MetrostationsResource::collection($metroStations);

    }

    public function supplies()
    {

        return SuppliesResource::collection(Supply::all());
    }
}
