<?php

namespace App\Helpers;

class DiscountCalculatorHelper
{
    /**
     * Paket üçün endirimli qiyməti hesablayır.
     *
     * @param float $basePrice  Paket qiyməti (30 günlük)
     * @param int $durationDays Davam etmə müddəti (günlər)
     * @return float
     */
    public static function calculateDiscountedPrice($basePrice, $durationDays):float
    {
        $discountRate = 0; // Standart endirim 0%

        if ($durationDays >= 365) { // 1 illik paket
            $discountRate = 0.30; // 30% endirim
        } elseif ($durationDays >= 180) { // 6 aylıq paket
            $discountRate = 0.15; // 15% endirim
        } elseif ($durationDays >= 90) { // 3 aylıq paket
            $discountRate = 0.10; // 10% endirim
        }

        // Endirim tətbiq olunur
        $discountedPrice = $basePrice * (1 - $discountRate);

        // Davamlılıq müddətinə uyğun **ümumi qiyməti hesablayırıq**
        return round(($discountedPrice / 30) * $durationDays, 2);
    }
}
