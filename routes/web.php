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
Route::get('/data', function () {
    $listings = \App\Models\Listing::where('ads_count', '>', 1)->orderBy('ads_count', 'desc')->get();
    $count = count($listings);

    echo "<h4>Cəmi {$count}</h4>";
    echo "<table border='1' cellspacing='0' cellpadding='8' style='width: 100%; text-align: left;'>";
    echo "<thead>";
    echo "<tr style='background-color: #f2f2f2;'>";
    echo "<th>#</th>";
    echo "<th>Ad</th>";
    echo "<th>Telefon</th>";
    echo "<th>Elan Sayı</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($listings as $index => $listing) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td>" . htmlspecialchars($listing->name) . "</td>";
        echo "<td>" . htmlspecialchars($listing->phone) . "</td>";
        echo "<td><b>" . htmlspecialchars($listing->ads_count) . "</b></td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
});
