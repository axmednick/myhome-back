<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AnnouncementVipPremium extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * VIP və ya Premium elan hələ aktivdir?
     */
    public function isActive(): bool
    {
        return Carbon::now()->lt($this->expires_at);
    }

}
