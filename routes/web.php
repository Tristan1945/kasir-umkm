<?php

use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Route;

Route::controller(KasirController::class)->group(function () {
    Route::get('/', 'index')->name('kasir.index');
    Route::post('/add-to-cart/{id}', 'addToCart')->name('kasir.add');
    Route::post('/remove-from-cart/{id}', 'removeFromCart')->name('kasir.remove');
    Route::post('/update-cart/{id}', 'updateCart')->name('kasir.update');
    Route::post('/checkout', 'checkout')->name('kasir.checkout');
});
