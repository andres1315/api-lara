<?php

use App\Http\Controllers\InveUbicacionController;


use Illuminate\Support\Facades\Route;


Route::post('/new_qty_product', [InveUbicacionController::class, 'newQtyProductLocation'])->name('new-qty-product');  // Modificar cantidad en ubicacion por consumo en [picking]
Route::post('/filter-products',[InveUbicacionController::class, 'filterProducts'])->name('filter-product');



