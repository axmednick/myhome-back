<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AnnouncementBoost extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * Bu elan 24 saatdan bir irəli çəkilməlidir?
     */
    public function shouldBoostAgain(): bool
    {
        return $this->remaining_boosts > 0 &&
            Carbon::parse($this->last_boosted_at)->addHours(24)->isPast();
    }
}
