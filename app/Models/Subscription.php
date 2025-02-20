<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $dates = ['start_date', 'end_date'];

    protected $casts=[
        'is_active'=>'boolean',
        'start_date'=>'datetime',
        'end_date'=>'datetime'
    ];

    /**
     * İstifadəçi ilə əlaqə (Əgər abunəlik bir user üçündürsə)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Agentlik ilə əlaqə (Əgər abunəlik bir agentlik üçündürsə)
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Paket ilə əlaqə
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Abunəliyin aktiv olub olmadığını yoxlayır
     */
    public function isActive()
    {
        return $this->is_active ;
    }

    /**
     * Subscription üçün bitmə tarixi keçibsə onu avtomatik deaktiv edək
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }
}
