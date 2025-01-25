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
                    ->width(344)
                    ->height(244)
                    ->fit('crop', 344, 244) // Crop işlemiyle tam boyutlandırma
                    ->optimize()
                    ->performOnCollections('image');

                $this
                    ->addMediaConversion('watermarked')
                    ->watermark(public_path('watermark.png'))
                    ->watermarkPosition('center')
                    ->width(1000) // Watermark ekledikten sonra resmin genişliği
                    ->height(1000) // Watermark ekledikten sonra resmin yüksekliği
                    ->optimize()
                    ->watermarkOpacity(20)
                    ->performOnCollections('image');
            });

        $this->addMediaCollection('main')

            ->singleFile() // Ensures only one image is in the "main" collection
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb_main')
                    ->width(344)
                    ->height(244)
                    ->fit('crop', 344, 244)
                    ->optimize()
                    ->performOnCollections('main');

                $this->addMediaConversion('watermarked')
                    ->watermark(public_path('watermark.png'))
                    ->watermarkPosition('center')
                    ->width(1000)
                    ->height(1000)
                    ->optimize()
                    ->watermarkOpacity(20)
                    ->performOnCollections('main');
            });

    }



}
