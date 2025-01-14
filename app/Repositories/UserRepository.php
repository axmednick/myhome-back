<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function find($id): ?User
    {
        return User::find($id);
    }

    public function findByAgency($agencyId)
    {
        return User::where('agency_id', $agencyId)->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
    public function updateMedia(User $user, $avatar = null): void
    {
        if ($avatar) {
            $user->clearMediaCollection('photo');
            $user->addMedia($avatar)->toMediaCollection('photo');
        }
    }
}
