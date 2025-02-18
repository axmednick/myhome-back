<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AnnouncementGridResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        try {
            $date = Carbon::parse($this->created_at)->format('d ') . trans('content.' . Carbon::parse($this->created_at)->format('F')) . Carbon::parse($this->created_at)->format(' Y');

            return [
                'id' => $this->id,
                'area' => $this->area,
                'room_count' => $this->room_count,
                'floor_count' => $this->floor_count,
                'floor' => $this->floor,
                'house_area' => $this->house_area,
                'description' => $this->description,
                'price' => isset($this->price) ? number_format((float) $this->price, 0, ',', ' ') : null,
                'user_id' => $this->user_id,
                'address' => AnnouncementAddressResource::make($this->address),
                'main_image_thumb' => $this->getMedia('image')->sortBy('order_column')->first()?->getUrl('thumb'),
                'title' => $this->title(),
                "formatted_date" => DateHelper::formatCreatedAt($this->created_at),
                'is_favorite' => Favorite::where('announcement_id', $this->id)->where('user_id', auth('sanctum')->id())->exists(),
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
