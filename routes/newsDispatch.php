<?php

use App\Http\Controllers\DespachoLogNovedadController;
use App\Http\Controllers\DespachoNovedadController;
use Illuminate\Support\Facades\Route;


//Route::get('/', [DespachoNovedadController::class, 'index'])->name('all-news-dispatch');  // Lista todo tipos de novedades
Route::get('/log', [DespachoLogNovedadController::class, 'show'])->name('log-news-dispatch');  // Lista las novedades de un despacho
Route::post('/log',[DespachoLogNovedadController::class,'store'])->name('add-log-news-dispatch');

