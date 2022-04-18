<?php

use App\Http\Controllers\API\v1\AvatarController;
use App\Http\Controllers\API\v1\OptionController;
use App\Http\Controllers\API\v1\ProfileController;
use App\Http\Controllers\API\v1\RegisterController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>'localization'], function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [RegisterController::class, 'login']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [RegisterController::class, 'logout']);
        Route::post('avatar', [AvatarController::class, 'avatar']);
        Route::post('profile', [ProfileController::class, 'profile']);
        Route::post('options', [OptionController::class, 'options']);
    });
});
