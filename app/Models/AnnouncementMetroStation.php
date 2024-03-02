<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementMetroStation extends Model
{
    use HasFactory;

    public function metro_station(){
        return $this->belongsTo(MetroStation::class);
    }
}
