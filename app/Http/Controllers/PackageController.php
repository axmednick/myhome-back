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


    public function rieltorPackages(): AnonymousResourceCollection
    {
        $packages = Package::where('type', 'realtor')->get();
        return PackageResource::collection($packages);
    }


}
