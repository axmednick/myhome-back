<?php

namespace App\Http\Controllers\Announcement;

use App\Enums\AnnouncementStatus;
use App\Helpers\SlackHelper;
use App\Helpers\TelegramHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\AnnouncementRequest;
use App\Http\Requests\ApartmentRequest;
use App\Http\Requests\HouseRequest;
use App\Http\Requests\LandRequest;
use App\Http\Requests\OfficeRequest;
use App\Http\Resources\AnnouncementBaseResource;
use App\Http\Resources\AnnouncementEditResource;
use App\Http\Resources\AnnouncementGridResource;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\MetrostationsResource;
use App\Http\Resources\SuppliesResource;
use App\Models\Announcement;
use App\Models\AnnouncementRentalClientTypes;
use App\Models\Favorite;
use App\Models\MetroStation;
use App\Models\Region;
use App\Models\Subscription;
use App\Models\Supply;
use App\Models\TemporaryFile;
use App\Models\User;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AnnouncementController extends Controller
{
    protected $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function store(Request $request)
    {

        if ($request->property_type == 1) {
            $validator = Validator::make($request->all(), (new ApartmentRequest)->rules());
        }
        if ($request->property_type == 2 || $request->property_type == 3 || $request->property_type == 4) {
            $validator = Validator::make($request->all(), (new HouseRequest)->rules());
        }
        if ($request->property_type == 5) {
            $validator = Validator::make($request->all(), (new LandRequest)->rules());
        }
        if ($request->property_type == 6 || $request->property_type == 7) {
            $validator = Validator::make($request->all(), (new OfficeRequest)->rules());
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!auth('sanctum')->check()) {

            $userValidator = Validator::make($request->all(), [
                'phone' => 'required|unique:users',
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'user_type' => 'required'
            ]);

            if ($userValidator->fails()) {
                return response()->json(['errors' => $userValidator->errors()], 422);
            }

            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type'=>$request->user_type,
                'register_type'=>'announcement',
            ]);

            if ($request->user_type == 'agent') {
                Subscription::create([
                    'user_id' => $user->id,
                    'package_id' => 4,
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays(30),
                    'is_active' => true,
                ]);
            }
        } else {

            $user = auth('sanctum')->user();
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
            'user_id' => $user->id,
            'price' => str_replace(' ', '', $request->price),
            'is_repaired' => $request->is_repaired,
            'document_id' => $request->property_document,
            'rental_type' => $request->rental_type,
            'looking_roommate' => $request->looking_roommate,
            'credit_possible' => $request->credit_possible,
            'in_credit' => $request->in_credit,
            'agency_id' => $user->agency ? $user->agency->id : null,
            'status' => AnnouncementStatus::Pending,
        ]);

        $announcement->address()->create([
            'announcement_id' => $announcement->id,
            'city_id' => $request->city,
            'region_id' => $request->region,
            'village_id' => $request->village,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address' => $request->address,

        ]);

        if ($request->has('client_types_for_rent') && is_array($request->client_types_for_rent)) {
            foreach ($request->client_types_for_rent as $client_type_for_rent) {
                AnnouncementRentalClientTypes::create([
                    'client_type_for_rent_id' => $client_type_for_rent,
                    'announcement_id' => $announcement->id
                ]);
            }
        }

        if ($request->has('supplies') && is_array($request->supplies)) {
            foreach ($request->get('supplies') as $supply) {
                $announcement->supplies()->create([
                    'supply_id' => $supply
                ]);
            }
        }

        if ($request->has('metro_stations') && is_array($request->metro_stations)) {
            foreach ($request->metro_stations as $metroStation) {
                $announcement->metro_stations()->create([
                    'metro_station_id' => $metroStation
                ]);
            }
        }
        if ($request->has('media_ids') && is_array($request->media_ids)) {
            foreach ($request->media_ids as $index => $mediaId) {
                $media = Media::where('model_id', $mediaId)
                    ->where('model_type', TemporaryFile::class)
                    ->first();
                if ($media) {
                    $media->update([
                        'model_type' => Announcement::class,
                        'model_id' => $announcement->id,
                        'order_column' => $index,
                    ]);
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'announcement' => AnnouncementResource::make($announcement),
            'token' => $user->createToken('MyApp')->plainTextToken
        ]);

    }


    public function announcements(Request $request)
    {
        $cacheKey = 'announcements_' . md5(serialize($request->all()));

        $announcements = $this->announcementService
                ->searchAnnouncements($request)
                ->with([
                    'announcement_type',
                    'media',
                    'property_type',
                    'apartment_type',
                    'address.village',
                    'address.region',
                    'address.city',
                    'user',
                    'supplies',
                    'rental_client_types',
                    'metro_stations'
                ])

                ->paginate(10);

        return AnnouncementGridResource::collection($announcements);
    }


    public function userAnnouncements(Request $request, $id = null)
    {


        $announcements = Announcement::where('user_id', auth('sanctum')->id())->orderBy('id', 'desc');

        if ($request->status) {

            $announcements->where('status', 1);
        }

        return AnnouncementResource::collection($announcements->paginate(12));
    }

    public function favorites(Request $request)
    {
        $favorite_announcement_ids = Favorite::where('user_id', auth('sanctum')->id())->pluck('announcement_id');


        $announcements = Announcement::whereIn('id', $favorite_announcement_ids);

        return AnnouncementResource::collection($announcements->paginate(12));
    }


    public function detail(Request $request,$id)
    {

        $announcement = $this->announcementService->announcementById($id,$request->type);

        return AnnouncementResource::make($announcement);
    }

    public function nearbyMetroStations(Request $request)
    {
        $region = Region::find($request->region_id);

        return MetrostationsResource::collection($region->metro_stations);

    }

    public function supplies()
    {

        return SuppliesResource::collection(Supply::all());
    }

    public function announcementPhone($id)
    {

        $announcement = Announcement::where('id', $id)->first();

        $user = $announcement->user;
        $user->phone_view_count = $user->phone_view_count + 1;
        $user->save();

        return  $user->phone;
    }

    public function similarAnnouncements($id)
    {

        return AnnouncementResource::collection($this->announcementService->similarAnnouncements($id));
    }

    public function link($link)
    {
        return AnnouncementResource::collection($this->announcementService->announcementsByLink($link));

    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);

        return AnnouncementEditResource::make($announcement);

    }

    public function update($id,Request $request)
    {
        $announcement = Announcement::findOrFail($id);

        if ($announcement->user_id!=auth('sanctum')->id()){
            return $this->sendError('Sizin bunu dəyişmək hüququnuz yoxdur',null,403);
        }

        if ($announcement->property_type_id == 1) {
            $validator = Validator::make($request->all(), (new ApartmentRequest)->rules());
        }
        if (in_array($announcement->property_type_id, [2, 3, 4])) {
            $validator = Validator::make($request->all(), (new HouseRequest)->rules());
        }
        if ($announcement->property_type_id == 5) {
            $validator = Validator::make($request->all(), (new LandRequest)->rules());
        }
        if (in_array($announcement->property_type_id, [6, 7])) {
            $validator = Validator::make($request->all(), (new OfficeRequest)->rules());
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $announcement->update([
            'apartment_type_id' => $request->apartment_type,
            'area' => $request->area ? $request->area : null,
            'house_area' => $request->house_area,
            'room_count' => $request->room_count ? intval($request->room_count) : null,
            'floor' => $request->floor ? intval($request->floor) : null,
            'floor_count' => $request->floor_count ? intval($request->floor_count) : null,
            'description' => $request->description,
            'price' => $request->price,
            'is_repaired' => $request->is_repaired,
            'document_id' => $request->property_document,
            'rental_type' => $request->rental_type,
            'looking_roommate' => $request->looking_roommate,
            'credit_possible' => $request->credit_possible,
            'in_credit' => $request->in_credit,
        ]);


        $incomingMediaIds = $request->media_ids ?? [];

        $existingMedia = $announcement->getMedia('images');


        foreach ($existingMedia as $media) {
            if (!in_array($media->id, $incomingMediaIds)) {
                // Əgər şəkil gələn ID-lərdə yoxdursa, silirik
                $media->delete();
            }
        }


        foreach ($incomingMediaIds as $index => $mediaId) {

            $media = Media::find($mediaId);


            if ($media) {
                $media->update([
                    'model_type' => Announcement::class,
                    'model_id' => $announcement->id
                ]);

                $media->update([
                    'order_column' => $index,
                ]);
            }
        }


        return $this->sendResponse(AnnouncementResource::make($announcement));

    }

    public function changeStatus(Request $request, Announcement $announcement)
    {
        $validate = Validator::make( $request->all(),[
            'status' => ['required', 'integer', Rule::in([1, 2])],
        ]);

        if ($validate->fails()) {
            return $this->sendError($validate->errors(), 'Validation Error', 422);
        }
        $newStatus = $request->status;
        $currentStatus = $announcement->status;

        if ($announcement->user_id !== auth('sanctum')->id()) {
            return $this->sendError('You are not authorized to update this announcement!', 'Unauthorized', 403);
        }

        if ($newStatus==AnnouncementStatus::Active && $currentStatus==AnnouncementStatus::Expired){
            $announcement->created_at=Carbon::now();
            $announcement->save();
        }


        $announcement->update(['status' => $newStatus]);



        return $this->sendResponse($newStatus, 'Announcement status updated successfully!', 200);
    }

}
