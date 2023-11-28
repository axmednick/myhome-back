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

    public function announcement_type()
    {
        return $this->belongsTo(AnnouncementType::class);
    }
    public function property_type()
    {
        return $this->belongsTo(PropertyType::class);
    }
    public function apartment_type()
    {
        return $this->belongsTo(ApartmentType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasOne(AnnouncementAddress::class);
    }

    public function rental_client_types()
    {
        return $this->hasMany(AnnouncementRentalClientTypes::class);
    }

    public function metro_stations(){
        return $this->hasMany(AnnouncementMetroStation::class);
    }
}
