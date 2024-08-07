<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryFile extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

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
        $this->addMediaConversion('original')->nonQueued();
    }

}
