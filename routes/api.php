<?php

use App\Http\Controllers\AIChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MobileAppController;
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


Route::prefix('v1')->group(function () {
    /**
     * Trading
     */
    Route::controller(TradingController::class)->group(function () {
        Route::get('/trading/cheap', 'cheap');
        Route::get('/trading/kapBuySellNotifitions', 'kapBuySellNotifitions');
    });

    Route::controller(MobileAppController::class)->group(function () {
        Route::post('/mobileApp/createKeyword', 'createKeyword');
        Route::get('/mobileApp/getKeywordList', 'getKeywordList');
        Route::post('/mobileApp/setLearnKeyword', 'setLearnKeyword');
        Route::get('/mobileApp/translate/{keyword}', 'translate');
        Route::get('/mobileApp/getKeyword/{id}', 'getKeyword');
        Route::post('/mobileApp/createCategory', 'createCategory');
        Route::get('/mobileApp/myKeywordCount', 'myKeywordCount');
        Route::get('/mobileApp/getCategory', 'getCategory');
    });

    Route::controller(AIChatController::class)->group(function () {
        Route::post('/aiChat/chatWithAI', 'chatWithAI');
        Route::post('/aiChat/chatWithAICustom', 'chatWithAICustom');
    });

});


