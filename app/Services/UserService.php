<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserService
{

    public function deductBalance(float $amount, ?User $user = null): void
    {
        $user = $user ?? auth()->user();

        if ($user->bonus_balance >= $amount) {
            $user->decrement('bonus_balance', $amount);
        } elseif ($user->balance >= $amount) {
            $user->decrement('balance', $amount);
        } else {
            throw new Exception('Insufficient balance', 402);
        }
    }
}
