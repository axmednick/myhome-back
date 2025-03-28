<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts=[
      'response'=>'array'
    ];

    public function user(){
      return $this->belongsTo(User::class);
    }
}
