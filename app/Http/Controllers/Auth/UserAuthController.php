<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                    'user'=>\auth('sanctum')->user(),
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

        return \auth('sanctum')->user();
    }

    public function verifyOtp(Request $request, $userId, $otp)
    {
        $result = $this->authService->verifyOtpAndMarkEmailVerified($userId, $otp);

        $token = Auth::loginUsingId($userId);


        if ($token) {

            return redirect('https://myhome.az?emailVerification=true&token=' . $token);

        } else {

            return redirect('https://myhome.az?emailVerification=false');
        }
    }

}
