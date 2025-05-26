<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// API routes for Instagram functionality
Route::prefix('api')->group(function () {

    // Get Instagram profile data
    Route::get('/profile', [HomeController::class, 'getProfile'])->name('api.profile');

    // Image proxy to bypass CORS
    Route::get('/proxy-image', [HomeController::class, 'proxyImage'])->name('api.proxy-image');

    // Download media
    Route::get('/download', [HomeController::class, 'downloadMedia'])->name('api.download');

});

// Alternative routes (if you prefer different URL structure)
// Route::group(['prefix' => 'instagram'], function () {
//     Route::get('/', [InstagramController::class, 'index']);
//     Route::get('/profile/{username}', [InstagramController::class, 'getProfile']);
//     Route::get('/image-proxy', [InstagramController::class, 'proxyImage']);
//     Route::get('/download-media', [InstagramController::class, 'downloadMedia']);
// });