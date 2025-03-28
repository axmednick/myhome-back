<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Agency extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $guarded=['id'];


    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Agentliyə aid olan istifadəçilərlə əlaqə
    public function users()
    {
        return $this->hasMany(User::class, 'agency_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
