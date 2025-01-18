<?php

namespace App\Services;

use App\Models\AgencyApply;
use App\Repositories\AgencyRepository;

class AgencyService
{
    protected AgencyRepository $repository;

    public function __construct(AgencyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function updateAgency($id, array $data, $logo = null, $coverPhoto = null)
    {
        $agency = $this->repository->find($id);

        if (!$agency) {
            throw new \Exception('Agency not found');
        }

        if ($agency->user_id !== auth('sanctum')->id()) {
            throw new \Exception('You are not authorized to update this agency');
        }
        $updatedAgency = $this->repository->update($agency, $data);

        if ($logo) {
            $this->repository->updateMedia($agency, $logo, null);
        }

        if ($coverPhoto) {
            $this->repository->updateMedia($agency, null, $coverPhoto);
        }

        return $updatedAgency;
    }

    public function apply($data)
    {
        $agencyApply = AgencyApply::create($data);

        return $agencyApply;
    }
    public function getAgency($id)
    {
        $agency = $this->repository->find($id);

        if (!$agency) {
            throw new \Exception('Agency not found');
        }

        return $agency;
    }




}
