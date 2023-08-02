<?php

use App\Http\Controllers\Announcement\AnnouncementController;
use App\Http\Controllers\GlobalDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('global')->group(function (){
   Route::get('announcement-types',[GlobalDataController::class,'announcementTypes']);
   Route::get('property-types',[GlobalDataController::class,'propertyTypes']);
   Route::get('apartment-types',[GlobalDataController::class,'apartmentTypes']);
   Route::get('cities',[GlobalDataController::class,'cities']);
   Route::get('city/{id}/regions',[GlobalDataController::class,'regions']);
   Route::get('region/{id}/villages',[GlobalDataController::class,'regions']);
   Route::get('rental-client-types',[GlobalDataController::class,'clientTypeForRents']);
   Route::get('metro-stations',[GlobalDataController::class,'metroStations']);

});

Route::prefix('announcement')->group(function (){

    Route::post('/store',[AnnouncementController::class,'store']);

});
