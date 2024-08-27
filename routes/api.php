<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;



Route::post('/auth', [AuthController::class, 'conf'])->middleware('setDatabase')->name('auth-client');
Route::post('/login', [AuthController::class, 'login'])->middleware('databaseHeader')->name('login');
