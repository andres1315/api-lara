<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', [UserController::class, 'index']);  // Lista todos los usuarios
Route::post('/user', [UserController::class, 'store']); // Crea un nuevo usuario
Route::get('/user/{id}', [UserController::class, 'show']); // Muestra un usuario específico
Route::put('/user/{id}', [UserController::class, 'update']); // Actualiza un usuario específico
Route::delete('/user/{id}', [UserController::class, 'destroy']); // Elimina un usuario específico
