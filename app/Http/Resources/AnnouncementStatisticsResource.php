<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'view_count' => $this->view_count,
            'phone_visible_count' => $this->phone_visible_count,
            'favorites_count' => Favorite::where('announcement_id', $this->announcement_id)->count(),
            'shared_count'=>55
        ];
    }
}
