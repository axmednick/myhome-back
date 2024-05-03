<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\SavedSearchResource;
use App\Services\SavedSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavedSearchController extends Controller
{
    public function __construct(protected SavedSearchService $savedSearchService)
    {
    }

    public function index()
    {
        return SavedSearchResource::collection($this->savedSearchService->allWithPaginated(12));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'url' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        $savedSearch = $this->savedSearchService->create($validator->validated());

        return response()->json(['success']);

    }

    public function delete($id)
    {
        $savedSearch = $this->savedSearchService->getById($id);

        $this->savedSearchService->delete($savedSearch);
    }
}
