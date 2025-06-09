<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('products.index');
})->name('products.index');

Route::get('/edit/{id}', function ($id) {
    return view('products.edit', ['id' => $id]);
})->name('products.edit');


include __DIR__ . '\api.php';
