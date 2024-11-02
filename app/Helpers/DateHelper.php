<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class DateHelper
{

    public static function formatCreatedAt($createdAt)
    {
        $createdAt = Carbon::parse($createdAt)->timezone(config('app.timezone')); // serverin saat qurşağına uyğun olaraq tarixi təyin edin

        $now = Carbon::now()->timezone(config('app.timezone')); // indiki vaxtı da uyğun saat qurşağında təyin edin

        $diff = $createdAt->diff($now);

        if ($diff->d == 0) {
            return "Bu gün " . $createdAt->format('H:i');
        } elseif ($diff->d == 1) {
            return "Dün " . $createdAt->format('H:i');
        } elseif ($diff->d > 1 && $diff->d < 7) {
            return $diff->d . " gün əvvəl " . $createdAt->format('H:i');
        } elseif ($diff->d >= 7 && $diff->d < 31) {
            $weeks = floor($diff->d / 7);
            return $weeks . " həftə əvvəl";
        } elseif ($diff->d >= 31) {
            $months = floor($diff->d / 30);
            return $months . " ay əvvəl";
        }
    }

}
