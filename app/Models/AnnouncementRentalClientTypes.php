<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementRentalClientTypes extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function type(){
        return $this->belongsTo(ClientTypeForRent::class,'client_type_for_rent_id','id');
    }
}
