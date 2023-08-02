<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementAddress extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function city(){
        return $this->belongsTo(City::class);
    }
    public function region(){
        return $this->belongsTo(Region::class);
    }
    public function village(){
        return $this->belongsTo(Village::class);
    }
    public function metroStation(){
        return $this->belongsTo(MetroStation::class);
    }
}
