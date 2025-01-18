<?php

namespace App\Http\Controllers;

use App\Services\AgencyUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgencyUserController extends Controller
{
    protected AgencyUserService $service;

    public function __construct(AgencyUserService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'photo' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = $this->service->createUser($validator->validated(), $request->file('photo')
            );
            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'photo' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = $this->service->updateUser($id, $validator->validated(), $request->file('photo'));
            return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function list()
    {
        try {
            $users = $this->service->listUsers();
            return response()->json(['users' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($id)
    {
        try {
            // İstifadəçini silmək üçün service funksiyasını çağırırıq
            $this->service->deleteUser($id);
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            // Xəta baş verərsə, cavabda xəta mesajını qaytarırıq
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
