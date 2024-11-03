<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class DateHelper
{

    public static function formatCreatedAt($createdAt)
    {
        $createdAt = Carbon::parse($createdAt)->timezone(config('app.timezone'));
        $now = Carbon::now()->timezone(config('app.timezone'));

        // Saniyə və dəqiqə fərqinə görə tam tarixi yoxlayırıq
        if ($now->isSameDay($createdAt)) {
            return "Bugün " . $createdAt->format('H:i');
        } elseif ($now->copy()->subDay()->isSameDay($createdAt)) {
            return "Dünən " . $createdAt->format('H:i');
        } else {
            // Daha uzun müddətli fərqləri həftələr və aylarla yoxlayırıq
            $diffInDays = $createdAt->diffInDays($now);

            if ($diffInDays < 7) {
                return $diffInDays . " gün əvvəl " . $createdAt->format('H:i');
            } elseif ($diffInDays >= 7 && $diffInDays < 31) {
                $weeks = floor($diffInDays / 7);
                return $weeks . " həftə əvvəl";
            } else {
                $months = floor($diffInDays / 30);
                return $months . " ay əvvəl";
            }
        }
    }



}
