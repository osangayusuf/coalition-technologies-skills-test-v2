<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::apiResource('products', 'App\Http\Controllers\ProductController');
});
