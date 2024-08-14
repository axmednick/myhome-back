<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementBaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'short_title' => $this->shortTitle(),
            'price' => number_format($this->price, 0, ',', ' '),
            'floor_count' => $this->floor_count,
            'floor' => $this->floor,
            'room_count' => $this->room_count,
            'document_id'=>$this->document_id,
            'is_repaired'=>$this->is_repaired,
            'looking_roommate'=>$this->looking_roommate,
            'credit_possible'=>$this->credit_possible,
            'in_credit'=>$this->in_credit,
            "formatted_date" => DateHelper::formatCreatedAt($this->created_at),
            'main_image_thumb' => $this->getFirstMediaUrl('image','thumb'),
            'images' => ImageResource::collection($this->getMedia('image')),
        ];
    }

    public function shortTitle()
    {

        if ($this->address->village) {
            return $this->address->village->name . ' qəsəbəsi';
        }
        if ($this->address->region) {
            return $this->address->region->name . ' rayonu';
        }
        if ($this->address->city) {
            return $this->address->city->name . ' şəhəri';
        }
    }
}
