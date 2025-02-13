<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PackageController extends Controller
{
    public function agencyPackages(): AnonymousResourceCollection
    {
        $packages = Package::where('type', 'agency')->get();
        return PackageResource::collection($packages);
    }

    /**
     * Rieltorlar üçün paketləri qaytarır.
     */
    public function realtorPackages(): AnonymousResourceCollection
    {
        $packages = Package::where('type', 'individual')->get();
        return PackageResource::collection($packages);
    }
}
