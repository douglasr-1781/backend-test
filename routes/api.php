<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('redirects', RedirectController::class);

Route::controller(RedirectController::class)->prefix('/redirects')->group(function() {
    Route::get('/{redirect}/stats', 'stats');
    Route::get('/{redirect}/logs', 'logs');
});