<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\Conversions\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;



class Announcement extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,SoftDeletes;
    protected $dates = ['deleted_at'];

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
        return $this->hasMany(AnnouncementMetroStation::class,'announcement_id');
    }

    public function supplies(){
        return $this->hasMany(AnnouncementSupply::class);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(500)
                    ->height(500)
                    ->optimize()
                    ->performOnCollections('image');

                $this
                    ->addMediaConversion('watermarked')
                    ->watermark(public_path('watermark.png'))
                    ->watermarkPosition(Manipulations::POSITION_CENTER)
                    ->width(1000) // Width of the image after adding watermark
                    ->height(1000) // Height of the image after adding watermark
                    ->optimize()
                    ->watermarkOpacity(25)
                    ->performOnCollections('image');
            });
    }
}
