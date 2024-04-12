<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StringHelper
{

    public static  function randomString(){


        $randomString = Str::random(10);
        $randomString = Str::random(10);


        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $max)];
        }

        return $randomString;
    }
}
