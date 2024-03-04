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
            'password' => 'nullable|min:6',
            'current_password' => 'required_with:password',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors()->messages());
        }


        if ($request->has('password') && $request->has('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['current_password' => 'The current password is incorrect.'], 422);
            }
            $user->password = Hash::make($request->password);
        }


        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return response()->json(['status'=>true,'user'=>User::find($user->id)]);
    }
}
