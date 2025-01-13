<?php

namespace App\Repositories;

use App\Models\Agency;

class AgencyRepository
{
    public function find($id): ?Agency
    {
        return Agency::find($id);
    }

    public function update(Agency $agency, array $data): Agency
    {
        $agency->update($data);

        return $agency;
    }

    public function updateMedia(Agency $agency, $logo = null, $coverPhoto = null): void
    {
        if ($logo) {
            $agency->clearMediaCollection('logo');
            $agency->addMedia($logo)->toMediaCollection('logo');
        }

        if ($coverPhoto) {
            $agency->clearMediaCollection('cover');
            $agency->addMedia($coverPhoto)->toMediaCollection('cover');
        }
    }

}
