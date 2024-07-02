<?php

namespace App\Http\Controllers;

use App\Http\Resources\BonusResource;
use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function progress()
    {

        return BonusResource::make(Bonus::find(1));
    }
}
