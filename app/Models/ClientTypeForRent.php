<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ClientTypeForRent extends Model
{
    use HasFactory,HasTranslations;
    protected $translatable=['name'];
}
