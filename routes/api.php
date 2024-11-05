<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TradingController;
use App\Http\Controllers\WeatherController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'auth'

], function ($router) {
    Route::controller(AuthController::class)->group(function () {
        Route::middleware([JwtMiddleware::class])->group(function () {
            Route::post('refresh', 'refresh');
            Route::post('me', 'me');
            Route::post('register', 'register');
        });
        Route::post('login', 'login');
        Route::post('logout', 'logout');
    });

});

Route::group([
    'prefix' => 'api'

], function ($router) {
    Route::prefix('v1')->group(function () {

        /**
         * Trading
         */
        Route::get('/trading/cheap', [TradingController::class,
                                      'cheap']);
        Route::get('/trading/kapBuySellNotifitions', [TradingController::class,
                                                      'kapBuySellNotifitions']);

    });
});

