<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'phone'=> $this->phone,
            'address'=> $this->address,
            'about'=> $this->about,
            'display_as_agency'=> $this->display_as_agency,
            'logo'=> $this->getFirstMediaUrl('logo'),
            'cover_photo'=> $this->getFirstMediaUrl('cover_photo'),

        ];
    }
}
