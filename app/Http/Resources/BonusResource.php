<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BonusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'announcement_count'=>$this->announcement_count,
            'bonus_amount'=>$this->bonus_amount,
            'user_announcements_count'=>12,
        ];
    }
}
