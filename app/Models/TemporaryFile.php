<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Conversions\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryFile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
                    ->watermarkPosition('center') // Mərkəzi mövqe təyin edin
                    ->width(1000) // Watermark əlavə edildikdən sonra şəkilin eni
                    ->height(1000) // Watermark əlavə edildikdən sonra şəkilin hündürlüyü
                    ->optimize()
                    ->watermarkOpacity(20)
                    ->performOnCollections('image');
            });

        $this->addMediaConversion('original')->nonQueued();
    }
}
