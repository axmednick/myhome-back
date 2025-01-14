<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [\App\Http\Controllers\DefaultController::class,'index']);

Route::get('/auth/{driver}/redirect', [App\Http\Controllers\GoogleLoginController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/{driver}/callback', [App\Http\Controllers\GoogleLoginController::class, 'handleCallback'])->name('google.callback');
Route::get('/data',function (){
    $listings = \App\Models\Listing::where('ads_count','>',1)->select('name','phone')->orderBy('ads_count','desc')->get();
    foreach ($listings as $listing){
        echo '<div>' . $listing->name .' '.$listing->phone.'</div>';
    }
});
