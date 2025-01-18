<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgencyUserService
{
    protected UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createUser(array $data, $photo = null)
    {
        $admin = \auth('sanctum')->user();

        if (!$admin->managedAgency) {
            throw new \Exception('You are not managing any agency');
        }

        $data['agency_id'] = $admin->managedAgency->id;
        $data['password'] = Hash::make($data['password']);
        $data['user_type'] = 'agent';

        $user = $this->repository->create($data);

        if ($photo) {
            $this->repository->updateMedia($user, $photo);
        }

        return $user;
    }


    public function updateUser($id, array $data, $photo = null)
    {
        $admin = \auth('sanctum')->user();
        $user = $this->repository->find($id);

        if (!$user || $user->agency_id !== $admin->managedAgency->id) {
            throw new \Exception('You are not authorized to update this user');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $this->repository->update($user, $data);

        if ($photo) {
            $this->repository->updateMedia($user, $photo);
        }

        return $user;
    }


    public function listUsers()
    {
        $admin = \auth('sanctum')->user();;

        if (!$admin->managedAgency) {
            throw new \Exception('You are not managing any agency');
        }

        return $this->repository->findByAgency($admin->managedAgency->id);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        \auth('sanctum')->user();
        if ($user->agency_id==auth('sanctum')->user()->managedAgency->id){
            $user->delete();
        }

        if (!$user) {
            throw new \Exception('User not found');
        }

        // İstifadəçini silirik

    }

}
