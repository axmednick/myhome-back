<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'name' => $this->name,
            'description' => 'test',
            'price' => $this->price,
            'listing_limit' => $this->listing_limit,
            'bonus' => $this->bonus,
            'type' => $this->type,
            'features' => $this->features->pluck('feature_name'), // Xüsusiyyətləri qaytarır
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
