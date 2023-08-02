<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'announcementType'=>AnnouncementTypeResource::make($this->announcementType),
            'propertyType'=>PropertyTypeResource::make($this->propertyType),
            'apartmentType'=>ApartmentTypeResource::make($this->apartmentType),
            'area'=>$this->area,
            'room_count'=>$this->room_count,
            'rental_type'=>$this->rental_type,
            'floor_count'=>$this->floor_count,
            'floor'=>$this->floor,
            'house_area'=>$this->house_area,

            'description'=>$this->description,
            'price'=>$this->price,
            'address'=>AnnouncementAddressResource::make($this->address),
            'images'=>ImageResource::collection($this->getMedia('image')),
            'main_image'=>$this->getFirstMediaUrl('image'),
            'user'=>$this->user,


        ];
    }
}
