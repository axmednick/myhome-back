<?php

namespace App\Http\Controllers;

use App\Http\Resources\BonusResource;
use App\Models\Bonus;
use App\Models\UserBonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function progress()
    {
        $user = auth('sanctum')->user();
        if (UserBonus::where('user_id', $user->id)->exists()) {
            return false;
        }
        $currentBonus = Bonus::where('announcement_count', '<', $user->announcements()->count())->first();
        return BonusResource::make($currentBonus);
    }

    public function take()
    {
        $user = auth('sanctum')->user();

        $bonus = Bonus::where('announcement_count', '<', $user->announcements()->count())->first();

        if ($bonus) {

           $bonus =  UserBonus::create([
                'user_id' => $user->id,
                'bonus_id' => $bonus->id
            ]);

           return $this->sendResponse($bonus, 'Bonus taken successfully');

        }

        else{
            return $this->sendError('Bonus not found',422);
        }
    }
}
