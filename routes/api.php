<?php

use App\Helpers\TelegramHelper;
use App\Http\Controllers\AdvertController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AgencyUserController;
use App\Http\Controllers\Announcement\AnnouncementController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\File\FileUploadController;
use App\Http\Controllers\GlobalDataController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\LinkController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SavedSearchController;
use App\Http\Controllers\User\UserAnnouncementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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



Route::controller(UserAuthController::class)->prefix('auth')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('user', 'user');
    Route::get('/email-verification/{userId}/{otp}', 'verifyOtp');
    Route::get('/google-one-tap', 'googleOneTapLogin');
    Route::post('/otp-check', 'otpCheck');
    Route::post('/resend-otp', 'reSend');
});


Route::prefix('user')->group(function () {

    Route::get('/announcements/{id?}', [AnnouncementController::class, 'userAnnouncements']);
    Route::get('/favorites', [AnnouncementController::class, 'favorites']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/bonus-progress', [BonusController::class, 'progress']);
        Route::get('/take-bonus', [BonusController::class, 'take']);
        Route::post('profile-update', [ProfileController::class, 'profileUpdate']);
        Route::post('/update-user-type', [ProfileController::class, 'updateUserType']);
        Route::post('password-update', [ProfileController::class, 'passwordUpdate']);
        Route::get('/statistics', [ProfileController::class, 'statistics']);
        Route::prefix('saved-search')->group(function (){
            Route::get('/',[SavedSearchController::class,'index']);
            Route::post('store',[SavedSearchController::class,'store']);
            Route::delete('{id}',[SavedSearchController::class,'delete']);
        });
        Route::prefix('announcement')->group(function () {
            Route::get('toggle-is-active/{id}', [UserAnnouncementController::class, 'toggleIsActive']);
            Route::delete('delete/{id}', [UserAnnouncementController::class, 'deleteAnnouncement']);
            Route::get('statistics/{id}', [UserAnnouncementController::class, 'announcementStatistics']);
            Route::get('/toggle-favorite/{id}', [FavoriteController::class, 'toggleFavorite']);
            Route::get('/edit/{id}', [AnnouncementController::class, 'edit']);
            Route::post('/update/{id}', [AnnouncementController::class, 'update']);
        });
        Route::prefix('link')->group(function () {
            Route::get('/', [LinkController::class, 'index']);
            Route::get('generate', [LinkController::class, 'generate']);
            Route::post('store', [LinkController::class, 'store']);
            Route::delete('delete/{id}', [LinkController::class, 'delete']);
        });
        Route::get('/agency',[AgencyController::class,'userAgency']);
    });
    Route::get('/phone/{id}',[UserAuthController::class,'getPhone']);
});

Route::get('announcement-types', [GlobalDataController::class, 'announcementTypes']);
Route::get('property-types', [GlobalDataController::class, 'propertyTypes']);
Route::get('apartment-types', [GlobalDataController::class, 'apartmentTypes']);
Route::get('cities', [GlobalDataController::class, 'cities']);
Route::get('city/{id}/regions', [GlobalDataController::class, 'regions']);
Route::get('region/{id}/villages', [GlobalDataController::class, 'villages']);
Route::get('rental-client-types', [GlobalDataController::class, 'clientTypeForRents']);
Route::get('metro-stations', [GlobalDataController::class, 'metroStations']);
Route::get('/all-regions', [GlobalDataController::class, 'allRegion s']);
Route::get('/all-villages', [GlobalDataController::class, 'allVillages']);
Route::get('/popular-categories', [GlobalDataController::class, 'popularCategories']);
Route::get('/static-pages', [GlobalDataController::class, 'staticPages']);
Route::get('/static-page/{slug}', [GlobalDataController::class, 'staticPage']);
Route::get('/meta-tags/{query}', [GlobalDataController::class, 'metaTags']);
Route::get('/agents',[GlobalDataController::class,'agents']);
Route::get('/agencies',[GlobalDataController::class,'agencies']);


Route::prefix('announcement')->group(function () {
    Route::post('/image-upload', [FileUploadController::class, 'temporaryFile']);
    Route::post('/store', [AnnouncementController::class, 'store']);
    Route::get('/list', [AnnouncementController::class, 'announcements']);
    Route::get('/item/{id}', [AnnouncementController::class, 'detail']);
    Route::get('/nearby-metro-stations', [AnnouncementController::class, 'nearbyMetroStations']);
    Route::get('/supplies', [AnnouncementController::class, 'supplies']);
    Route::get('/phone/{id}', [AnnouncementController::class, 'announcementPhone']);
    Route::post('/make-vip', [AdvertController::class, 'makeVip']);
    Route::post('/make-premium', [AdvertController::class, 'makePremium']);
    Route::get('/similar/{id}', [AnnouncementController::class, 'similarAnnouncements']);
    Route::get('/link/{code}', [AnnouncementController::class, 'link']);
});

Route::prefix('agency')->group(function (){
   Route::post('/update/{id}',[AgencyController::class,'update']);
   Route::get('/detail/{id}',[AgencyController::class,'detail']);
    Route::prefix('users')->group(function () {
        Route::post('/create', [AgencyUserController::class, 'create']);
        Route::post('/update/{id}', [AgencyUserController::class, 'update']);
        Route::get('/', [AgencyUserController::class, 'list']);
        Route::delete('/{id}', [AgencyUserController::class, 'delete']);
    });

    Route::post('apply',[AgencyController::class,'apply']);
});

Route::prefix('payment')->group(function (){
   Route::post('/pay',[PaymentController::class,'createOrder']);
    Route::get('/callback',[PaymentController::class,'callbackTransaction']);
});
