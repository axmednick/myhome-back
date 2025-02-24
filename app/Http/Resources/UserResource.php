<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
          'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'email_verified_at'=>$this->email_verified_at,
            'user_type'=>$this->user_type,
            'phone'=>$this->phone,
            'image'=>$this->avatar,
            'balance'=>$this->balance,
            'bonus_balance'=>$this->bonus_balance,
            'photo'=>$this->getMedia('photo') ? $this->getFirstMediaUrl('photo') : null,
            'is_verified' => $this->is_verified, // Blue Tick statusu
            'is_gold_user' => $this->is_gold_user, // Gold User statusu
            'is_agency_admin' => $this->managedAgency ? true : false,
            'agency' => $this->agency ? new AgencyResource($this->agency) : null,
            'has_subscription' => $this->activeSubscription() ? $this->activeSubscription()->exists() : false,
        ];
    }
}
