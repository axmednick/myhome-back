<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaidServiceResource;
use App\Models\PaidService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaidServiceController extends Controller
{
    /**
     * Ödənişli xidmətlərin siyahısını qaytarır.
     */
    public function index()
    {
        $services = PaidService::with('options')->get();

        return PaidServiceResource::collection($services);
    }

    /**
     * Müəyyən bir xidməti qaytarır.
     */
    public function show($id)
    {
        $service = PaidService::with('options')->find($id);

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Xidmət tapılmadı'
            ], 404);
        }

        return  PaidServiceResource::make($service);
    }
}
