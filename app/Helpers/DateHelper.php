<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class DateHelper
{

    public static function formatCreatedAt($createdAt)
    {
        $createdAt = Carbon::parse($createdAt)->timezone(config('app.timezone'));
        $now = Carbon::now()->timezone(config('app.timezone'));

        $diffInDays = $createdAt->diffInDays($now);
        $diffInHours = $createdAt->diffInHours($now);

        if ($diffInDays == 0 && $diffInHours < 24) {
            return "Bu gün " . $createdAt->format('H:i');
        } elseif ($diffInDays == 1) {
            return "Dün " . $createdAt->format('H:i');
        } elseif ($diffInDays > 1 && $diffInDays < 7) {
            return $diffInDays . " gün əvvəl " . $createdAt->format('H:i');
        } elseif ($diffInDays >= 7 && $diffInDays < 31) {
            $weeks = floor($diffInDays / 7);
            return $weeks . " həftə əvvəl";
        } elseif ($diffInDays >= 31) {
            $months = floor($diffInDays / 30);
            return $months . " ay əvvəl";
        }
    }


}
