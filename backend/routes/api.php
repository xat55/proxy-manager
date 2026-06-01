<?php

use App\Http\Controllers\Api\ProxyController;
use App\Http\Controllers\Docs\Swagger\ProxyController as SwaggerController;
use Illuminate\Support\Facades\Route;

Route::name('swagger.')->group(function () {
    Route::get('docs', [SwaggerController::class, 'index'])->name('index');
    Route::get('docs/json', [SwaggerController::class, 'json'])->name('json');
});

Route::post('proxies/check-all', [ProxyController::class, 'checkAll'])->name('proxies.check-all');
Route::apiResource('proxies', ProxyController::class);
Route::post('proxies/{proxy}/check', [ProxyController::class, 'check'])->name('proxies.check');
