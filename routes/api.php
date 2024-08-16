<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\SetClientDatabase;
use Illuminate\Support\Facades\Route;



Route::post('/auth', [AuthController::class, 'conf'])->middleware('setDatabase');
Route::post('/login', [AuthController::class, 'login'])->middleware('databaseHeader');

