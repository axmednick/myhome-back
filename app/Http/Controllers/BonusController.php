<?php

namespace App\Http\Controllers;

use App\Http\Resources\BonusResource;
use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function progress()
    {
        $user = auth('sanctum')->user();
        $currentBonus = Bonus::where('announcement_count', '>', $user->announcements()->count())->first();
        return BonusResource::make($currentBonus);
    }

    public function take()
    {
        $user = auth('sanctum')->user();
        $bonus = Bonus::find(1);
        if ($user->announcements()->count() >= $bonus->announcement_count) {

            return response()->json(['message' => 'Bonus taken']);
        }
        else{

        }
    }
}
