<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\User;
use App\Services\AuthService;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserAuthController extends Controller
{

    public $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'phone' => 'unique:users'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        $this->authService->sendOtpToEmail($user);

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;

            return response()->json([
                'token' => $success['token'],
                'user' => UserResource::make($user),
                'name' => $user->name,
                'message' => 'User login successfully.',
                'success' => true
            ]);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function user(Request $request)
    {

        return UserResource::make(\auth('sanctum')->user());
    }

    public function verifyOtp(Request $request, $userId, $otp)
    {
        $result = $this->authService->verifyOtpAndMarkEmailVerified($userId, $otp);


        if ($result) {

            $user = User::findOrFail($userId);

            $token = $user->createToken('MyApp')->plainTextToken;

            return redirect('https://myhome.az/giris?emailVerification=true&token=' . $token);

        } else {

            return redirect('https://myhome.az?emailVerification=false');
        }
    }


    public function googleOneTapLogin(Request $request)
    {
        $client = new Google_Client(['client_id' => '221758298387-hum5vconak66a3jd53s67m41nmseok4j.apps.googleusercontent.com']);  // Specify the CLIENT_ID of the app that accesses the backend
        $googleUser = $client->verifyIdToken($request->token);

        $user = User::where('email', $googleUser['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser['name'],
                'email' => $googleUser['email'],
                'password' => Hash::make(rand(100000, 999999)),
                'register_type' => 'google'
            ]);

            if ($googleUser['picture']) {
                $user->addMediaFromUrl($googleUser['picture'])->toMediaCollection('photo');
            }
        } else {

        }
        if ($user->getFirstMediaUrl('photo') == '') {
            if ($googleUser['picture']) {
                $user->addMediaFromUrl($googleUser['picture'])->toMediaCollection('photo');
            }
        }

        $user->login_type = 'google';
        $user->save();
        $token = $user->createToken('AccessToken')->plainTextToken;

        return response()->json(['token' => $token, 'user' => UserResource::make($user)]);
    }


    public function otpCheck(Request $request)
    {
        $user = \auth('sanctum')->user();

        $otp = Otp::where('otp_code', $request->otp)->where('user_id', $user->id)->first();
        if ($otp) {
            $user->email_verified_at = now();
            $user->save();
            return $this->sendResponse([], 'Otp is correct');
        } else {
            return $this->sendError('Otp is incorrect', ['error' => 'Otp is incorrect'], 401);
        }
    }

    public function reSend()
    {

    }

}
