<?php

use App\Http\Controllers\Api\GetOrderBookController;
use App\Http\Controllers\Api\SetOrderController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware('auth:sanctum')->group(function () {
    Route::post('/order', SetOrderController::class)
        ->name('order.set');

    Route::get('/order-book', GetOrderBookController::class)
        ->name('order-book');
});
