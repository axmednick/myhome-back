<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidService extends Model
{
    use HasFactory;

    public function options()
    {
        return $this->hasMany(PaidServiceOption::class, 'service_id');
    }
}
