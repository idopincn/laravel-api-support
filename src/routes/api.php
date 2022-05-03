<?php

use Illuminate\Support\Facades\Route;
use Idopin\ApiSupport\Controllers\AuthorizationsController;
use Idopin\ApiSupport\Controllers\FilesController;
use Idopin\ApiSupport\Controllers\CaptchasController;
use Idopin\ApiSupport\Controllers\SmsController;



Route::prefix('api')->group(function () {

    // 用户登录
    Route::post('authorizations', [AuthorizationsController::class, 'store'])->name('authorizations.store');

    // 取得已登录的用户ID
    Route::get('authorizations/id', [AuthorizationsController::class, 'id'])->name('authorizations.id');

    // 用户注销登录
    Route::delete('authorizations', [AuthorizationsController::class, 'destroy'])->name('authorizations.delete');

    Route::get('/files/{file}', [FilesController::class, 'show']);
    Route::post('/files', [FilesController::class, 'store']);
    Route::post('/files/check', [FilesController::class, 'check']);

    Route::post('captchas', [CaptchasController::class, 'store']);

    Route::post('sms', [SmsController::class, 'store']);
});
