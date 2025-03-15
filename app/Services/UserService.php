<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserService
{

    public function deductBalance(float $amount, bool $allowBonus = true, ?User $user = null): void
    {
        $user = $user ?? auth()->user();

        if ($allowBonus && $user->bonus_balance >= $amount) {
            $user->decrement('bonus_balance', $amount);
        } elseif ($user->balance >= $amount) {
            $user->decrement('balance', $amount);
        } else {
            abort(402, 'Insufficient balance');
        }
    }

}
