<?php

namespace App\Http\Controllers;


use App\Services\AgencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AgencyController extends Controller
{
    protected AgencyService $agencyService;

    public function __construct(AgencyService $service)
    {
        $this->agencyService = $service;
    }

    /**
     * Update agency information.
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15',
            'address' => 'sometimes|string|max:255',
            'about' => 'sometimes|string|nullable',
            'display_as_agency' => 'sometimes|boolean',
            'logo' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048',
            'cover_photo' => 'sometimes|file|mimes:jpg,jpeg,png|max:4096',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $agency = $this->agencyService->updateAgency(
                $id,
                $validator->validated(),
                $request->file('logo'),
                $request->file('cover_photo')
            );

            return $this->sendResponse($agency,'Agency Updated successfully!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}