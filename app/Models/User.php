<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }


    public function managedAgency()
    {
        return $this->hasOne(Agency::class, 'user_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id')->where('is_active', true);
    }

    public function agencySubscription()
    {
        return $this->hasOne(Subscription::class, 'agency_id', 'agency_id')->where('is_active', true);
    }

    public function activeSubscription()
    {
        if ($this->user_type !== 'agent') {
            return false;
        }

        return $this->agency_id ? $this->agencySubscription() : $this->subscription();
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($user) {
            if ((is_null($user->getOriginal('user_type')) || $user->getOriginal('user_type') == 'user') && $user->user_type == 'agent') {
                $existingSubscription = Subscription::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->first();
                if (!$existingSubscription) {
                    Subscription::create([
                        'user_id' => $user->id,
                        'package_id' => 4,
                        'start_date' => Carbon::now(),
                        'end_date' => Carbon::now()->addDays(30),
                        'is_active' => true,
                    ]);
                    Log::info("Subscription created for user_id: {$user->id}");
                }
            }
        });
    }


}
