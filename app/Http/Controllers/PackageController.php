<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageInfoResource;
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
    public function rieltorPackages(): AnonymousResourceCollection
    {
        $packages = Package::where('type', 'realtor')->get();
        return PackageResource::collection($packages);
    }
    public function userPackageInfo()
    {
        $user = auth('sanctum')->user();

        if ($user->isAgencyAdmin() && $user->agency->package) {
            return PackageInfoResource::make($user->agency->package);
        }

        if ($user->user_type === 'agent' && !is_null($user->package_id)) {
            return PackageInfoResource::make($user->package);
        }

        return response()->json([
            'status' => false,
            'message' => 'İstifadəçi üçün paket tapılmadı'
        ]);
    }

}
