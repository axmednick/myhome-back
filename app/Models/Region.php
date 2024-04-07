<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasFactory,HasTranslations;
    protected $translatable = ['name'];

    public function villages(){
        return $this->hasMany(Village::class);
    }
    public function metro_stations(){
        return $this->belongsToMany(MetroStation::class,'region_metro_stations');
    }
}
