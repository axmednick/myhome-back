<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\AlignPosition;
use Spatie\MediaLibrary\Conversions\Manipulations;
use Spatie\MediaLibrary\Conversions\Manipulations as SpatieManipulations;
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
                    ->watermark(public_path('watermark.png'),AlignPosition::Center)
                    ->width(1000) // Watermark eklendikten sonra resmin genişliği
                    ->height(1000) // Watermark eklendikten sonra resmin yüksekliği
                    ->optimize()
                    ->performOnCollections('image');
            });

        $this->addMediaConversion('original')->nonQueued();
    }
}
