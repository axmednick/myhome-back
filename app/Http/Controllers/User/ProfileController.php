<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profileUpdate(Request $request)
    {
        $user = auth('sanctum')->user();

        $validate = Validator::make($request->all(), [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors()->messages());
        }





        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->user_type = $request->user_type;
        $user->save();

        return response()->json(['status'=>true,'user'=>User::find($user->id)]);
    }


    public function passwordUpdate(Request $request){

        $user = auth('sanctum')->user();

        $validate = Validator::make($request->all(), [
            'password' => 'required|min:6',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors()->messages());
        }

        $user->password=Hash::make($request->password);
        $user->save();
        return response()->json(['status'=>true,'user'=>User::find($user->id)]);


    }
}
