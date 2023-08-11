<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Announcement extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $guarded = ['id'];

    public function announcementType()
    {
        return $this->belongsTo(AnnouncementType::class);
    }
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
    public function apartmentType()
    {
        return $this->belongsTo(AnnouncementType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasOne(AnnouncementAddress::class);
    }

    public function rentalClientTypes()
    {
        return $this->hasMany(AnnouncementRentalClientTypes::class);
    }

    public function metroStations(){
        return $this->hasMany(AnnouncementMetroStation::class);
    }
}
