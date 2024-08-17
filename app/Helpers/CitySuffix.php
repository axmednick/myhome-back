<?php

namespace App\Helpers;

class CitySuffix
{
    public static function getSuffix(string $cityName): string
    {
        $softVowels = ['e', 'ə', 'i', 'ö', 'ü'];

        $lastChar = mb_substr($cityName, -1);

        return in_array(mb_strtolower($lastChar), $softVowels) ? 'də' : 'da';
    }

    public static function cityWithSuffix(string $cityName): string
    {
        $suffix = self::getSuffix($cityName);
        return $cityName . $suffix;
    }
}
