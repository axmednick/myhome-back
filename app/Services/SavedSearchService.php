<?php

namespace App\Services;

use App\Models\SavedSearch;

class SavedSearchService
{

    public function create(array $data)
    {

        $data['user_id'] = auth('sanctum')->id();

        $savedSearch = SavedSearch::create($data);

        return $savedSearch;
    }

    public function allWithPaginated($perpage)
    {
        return SavedSearch::where('user_id', auth('sanctum')->id())->orderBy('id', 'desc')->paginate($perpage);
    }

    public function getById($id)
    {
        return SavedSearch::findOrFail($id);
    }

    public function delete(SavedSearch $savedSearch)
    {
        $savedSearch->delete();
    }

}
