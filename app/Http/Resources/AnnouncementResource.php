<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use App\Models\Favorite;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        try {
            $date = Carbon::parse($this->created_at)->format('d ') . trans('content.' . Carbon::parse($this->created_at)->format('F')) . Carbon::parse($this->created_at)->format(' Y');

            return [
                'id' => $this->id,
                'announcement_type' => AnnouncementTypeResource::make($this->announcement_type),
                'property_type' => PropertyTypeResource::make($this->property_type),
                'apartment_type' => ApartmentTypeResource::make($this->apartment_type),
                'area' => $this->area,
                'room_count' => $this->room_count,
                'rental_type' => $this->rental_type,
                'floor_count' => $this->floor_count,
                'floor' => $this->floor,
                'house_area' => $this->house_area,
                'description' => $this->description,
                'price' => isset($this->price) ? number_format((float) $this->price, 0, ',', ' ') : null,
                'price_per_square' => $this->house_area ? round($this->price / $this->house_area) : 0,
                'user_id' => $this->user_id,
                'address' => AnnouncementAddressResource::make($this->address),
                'images' => ImageResource::collection($this->getMedia('image')),
                'main_image' => $this->getFirstMediaUrl('main') ?: $this->getFirstMediaUrl('image'),
                'main_image_thumb' => $this->getMedia('image')->sortBy('order_column')->first()?->getUrl('thumb'),
                'user' => UserResource::make($this->user),
                'short_title' => $this->shortTitle(),
                'title' => $this->title(),
                'short_details' => $this->shortDetails(),
                'date' => $date,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                "formatted_date" => DateHelper::formatCreatedAt($this->created_at),
                'view_count' => $this->view_count,
                'is_active' => $this->is_active,
                'is_favorite' => Favorite::where('announcement_id', $this->id)->where('user_id', auth('sanctum')->id())->exists(),
                'supplies' => AnnouncementSuppliesResource::collection($this->supplies),
                'client_types_for_rent' => RentalClientTypesResource::collection($this->rental_client_types),
                'looking_roommate' => $this->looking_roommate,
                'credit_possible' => $this->credit_possible,
                'in_credit' => $this->in_credit,
                'metro_stations' => AnnouncementMetroStationsResource::collection($this->metro_stations),
                'status' => $this->status,
                'document_id' => $this->document_id,
                'is_repaired' => $this->is_repaired,
                'is_vip'=> $this->is_vip,
                'is_premium'=> $this->is_premium,

            ];
        } catch (\Exception $e) {
            Log::error("Announcement ID {$this->id} has an error: " . $e->getMessage());
            return [
                'id' => $this->id,
                'error' => 'Price information could not be retrieved'
            ];
        }
    }


    public function shortTitle()
    {

        if (isset($this->address->village)) {
            return $this->address->village->name . ' qəsəbəsi';
        }
        if (isset($this->address->region)) {
            return $this->address->region->name . ' rayonu';
        }
        if (isset($this->address->city)) {
            return $this->address->city->name . ' şəhəri';
        }
    }

    public function title()
    {

        $title = " ";
        if (isset($this->address->village)) {
            $title .= $this->address->village->name . ' qəsəbəsi';
        } elseif (isset($this->address->region)) {
            $title .= $this->address->region->name . ' rayonu';
        } elseif (isset($this->address->city)) {
            $title .= $this->address->city->name . ' şəhəri';
        }

        if ($this->house_area) {
            $title .= " " . $this->house_area . "m²";
        } elseif ($this->house_area) {
            $title .= " " . $this->area . "m²";
        }

        if ($this->property_type_id == 1) {
            $title .= " mənzil";
        }
        if ($this->property_type_id == 2) {
            $title .= " həyət evi";
        }
        if ($this->property_type_id == 3) {
            $title .= " villa";
        }
        if ($this->property_type_id == 4) {
            $title .= " bağ evi";
        }
        if ($this->property_type_id == 5) {
            $title .= " torpaq";
        }
        if ($this->property_type_id == 6) {
            $title .= " ofis";
        }
        if ($this->property_type_id == 7) {
            $title .= " obyekt";
        }


        if ($this->announcement_type_id == 1) {
            $title .= " satılır";
        }
        if ($this->announcement_type_id == 2) {
            $title .= " kirayə verilir";
        }


        return $title;
    }


    public function shortDetails()
    {
        $details = [];

        if ($this->room_count) {
            array_push($details, $this->room_count . ' otaqlı');
        }

        if ($this->house_area) {
            array_push($details, $this->house_area . ' m²');
        }

        if (!$this->house_area && $this->area) {
            array_push($details, $this->area . ' m²');
        }

        if ($this->floor_count && $this->floor) {
            array_push($details, $this->area . ' m²');
        }

        return $details;
    }
}
