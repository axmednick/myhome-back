<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
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

            'email_verified_at' => $this->email_verified_at,
            'user_type' => $this->user_type,

            'image' => $this->avatar,
            'photo' => $this->getMedia('photo')->isNotEmpty()
                ? $this->getFirstMediaUrl('photo')
                : asset('images/default-photo.jpg'),
            'is_verified' => $this->is_verified, // Blue Tick statusu
            'is_gold_user' => $this->is_gold_user, // Gold User statusu
            'announcement_count' => $this->announcements_count, // Elan sayı

        ];
    }
}
