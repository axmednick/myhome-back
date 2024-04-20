<?php

namespace App\Services;

use App\Mail\UserRegisteredMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;


class AuthService
{
    public function sendOtpToEmail($user)
    {
        $otp = Otp::create([
            'user_id' => $user->id,
            'otp_code' => $this->generateOtp(),
            'otp_expiry' => now()->addMinutes(config('auth.otp_expiry')),
        ]);

        Mail::to($user->email)->queue(new UserRegisteredMail($user,$otp));


        return true;
    }


    private function generateOtp()
    {
        return mt_rand(1000, 9999);
    }

    public function verifyOtpAndMarkEmailVerified($userId, $otp)
    {

        $user = User::findOrFail($userId);


        $otpRecord = Otp::where('user_id', $user->id)
            ->where('otp_code', $otp)
            ->where('otp_expiry', '>', now())
            ->first();

        if (!$otpRecord) {
            return false;
        }


        $user->email_verified_at = now();
        $user->save();

        $otpRecord->delete();

        return true;
    }
}
