<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidServiceOption extends Model
{
    use HasFactory;

    public function service()
    {
        return $this->belongsTo(PaidService::class, 'service_id');
    }
}
