<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementTypeResource;
use App\Http\Resources\ApartmentTypeResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\MetrostationsResource;
use App\Http\Resources\PopularCategoryResource;
use App\Http\Resources\PropertyTypeResource;
use App\Http\Resources\RegionsAndVillageResource;
use App\Http\Resources\RentalClientTypesResource;
use App\Http\Resources\StaticPageResource;
use App\Http\Resources\VillageResource;
use App\Models\AnnouncementRentalClientTypes;
use App\Models\AnnouncementType;
use App\Models\ApartmentType;
use App\Models\City;
use App\Models\ClientTypeForRent;
use App\Models\MetroStation;
use App\Models\PopularCategory;
use App\Models\PropertyType;
use App\Models\Region;
use App\Models\StaticPage;
use App\Models\Village;
use App\Services\MetaTagsService;
use Illuminate\Http\Request;

class GlobalDataController extends Controller
{

    public $metaTagsService;
    public function __construct(MetaTagsService $metaTagsService)
    {
        $this->metaTagsService = $metaTagsService;
    }

    public function announcementTypes(){
        return AnnouncementTypeResource::collection(AnnouncementType::all());
    }
    public function propertyTypes(){
        return PropertyTypeResource::collection(PropertyType::all());
    }
    public function apartmentTypes(){
        return ApartmentTypeResource::collection(ApartmentType::all());
    }
    public function cities(){
        return CityResource::collection(City::all());
    }
    public function regions($cityId){
        return CityResource::collection(Region::where('city_id',$cityId)->get());
    }

    public function clientTypeForRents(){

        return RentalClientTypesResource::collection(ClientTypeForRent::all());
    }

    public function metroStations(){
        return MetrostationsResource::collection(MetroStation::all());
    }
    public function villages($regionId){
        return CityResource::collection(Village::where('region_id',$regionId)->get());
    }

    public function allRegions(){
        return  RegionsAndVillageResource::collection(Region::all());
    }
    public function allVillages(){
        return  VillageResource::collection(Village::orderBy('region_id')->get());
    }

    public function popularCategories(){
        return PopularCategoryResource::collection(PopularCategory::all());
    }

    public function staticPages(){
        $staticPages = StaticPage::all();
        return StaticPageResource::collection($staticPages);
    }
    public function staticPage($slug){
        $staticPage = StaticPage::where('slug',$slug)->first();
        return StaticPageResource::make($staticPage);
    }


    public function metaTags($query){
        return $this->metaTagsService->getMetaTags($query);
    }
}
