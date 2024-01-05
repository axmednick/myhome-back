<?php

use App\Http\Controllers\Announcement\AnnouncementController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\File\FileUploadController;
use App\Http\Controllers\GlobalDataController;
use App\Http\Controllers\User\UserAnnouncementController;
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

Route::controller(UserAuthController::class)->prefix('auth')->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('user', 'user');
});


Route::prefix('user')->group(function (){

   Route::get('/announcements/{id?}',[AnnouncementController::class,'userAnnouncements']);
   Route::middleware('auth:sanctum')->group(function (){
       Route::prefix('announcement')->group(function (){
           Route::post('toggle-is-active/{id}',[UserAnnouncementController::class,'toggleIsActive']);
           Route::delete('delete/{id}',[UserAnnouncementController::class,'deleteAnnouncement']);
           Route::get('statistics/{id}',[UserAnnouncementController::class,'announcementStatistics']);
           Route::get('/toggle-favorite/{id}',[FavoriteController::class,'toggleFavorite']);
       });


   });

});


   Route::get('announcement-types',[GlobalDataController::class,'announcementTypes']);
   Route::get('property-types',[GlobalDataController::class,'propertyTypes']);
   Route::get('apartment-types',[GlobalDataController::class,'apartmentTypes']);
   Route::get('cities',[GlobalDataController::class,'cities']);
   Route::get('city/{id}/regions',[GlobalDataController::class,'regions']);
   Route::get('region/{id}/villages',[GlobalDataController::class,'villages']);
   Route::get('rental-client-types',[GlobalDataController::class,'clientTypeForRents']);
   Route::get('metro-stations',[GlobalDataController::class,'metroStations']);
   Route::get('/all-regions',[GlobalDataController::class,'allRegions']);
   Route::get('/all-villages',[GlobalDataController::class,'allVillages']);


Route::prefix('announcement')->group(function (){
    Route::post('/image-upload',[FileUploadController::class,'temporaryFile']);
    Route::post('/store',[AnnouncementController::class,'store']);
    Route::get('/list',[AnnouncementController::class,'announcements']);
    Route::get('/item/{id}',[AnnouncementController::class,'detail']);


});
