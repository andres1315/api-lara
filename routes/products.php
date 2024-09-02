<?php

use App\Http\Controllers\ProductoController;

use Illuminate\Support\Facades\Route;


Route::get('/all-location/{id}', [ProductoController::class, 'locationByProducto'])->name('all-location-product');  // Ubicaciones Inventorio por producto



