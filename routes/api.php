<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TradingController;
use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request;
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
/*
Route::middleware('auth:sanctum')
     ->get('/user', function(Request $request) {
         return $request->user();
     });
*/
Route::prefix('v1')
     ->group(function() {

         /** Auth route */
         Route::post('/auth/login', [AuthController::class, 'login']);
         Route::post('/auth/logout', [AuthController::class, 'logout']);
         Route::post('/auth/refresh', [AuthController::class, 'refresh']);
         Route::post('/auth/me', [AuthController::class, 'me']);

         /**
          * Weather
          */
         Route::get('/weather/current', [WeatherController::class, 'current']);

         /**
          * Trading
          */
         Route::get('/trading/cheap', [TradingController::class, 'cheap']);
         Route::get('/trading/kapBuySellNotifitions', [TradingController::class, 'kapBuySellNotifitions']);


     });

Route::get('/v1', function() {
    return [
        'data' => 'Welcome to the Desert of the Real'
    ];
});



