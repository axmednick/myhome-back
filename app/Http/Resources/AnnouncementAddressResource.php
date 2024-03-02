<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'address'=>$this->address,
            'city'=>CityResource::make($this->city),
            'region'=>CityResource::make($this->region),
            'village'=>CityResource::make($this->village),
            'lat'=>$this->lat,
            'lng'=>$this->lng,


        ];
    }
}
