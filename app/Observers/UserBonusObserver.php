<?php

namespace App\Observers;

use App\Models\Bonus;
use App\Models\User;
use App\Models\UserBonus;

class UserBonusObserver
{
    public function created(UserBonus $userBonus)
    {
        $user = User::find($userBonus->user_id);
        $bonus = Bonus::find($userBonus->bonus_id);

        $user->bonus_balance += $bonus->bonus_amount;
        $user->save();

    }


}
