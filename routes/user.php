<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index']);  // Lista todos los usuarios
Route::post('/', [UserController::class, 'store']); // Crea un nuevo usuario
Route::get('/{id}', [UserController::class, 'show']); // Muestra un usuario específico
Route::put('/{id}', [UserController::class, 'update']); // Actualiza un usuario específico
Route::delete('/{id}', [UserController::class, 'destroy']); // Elimina un usuario específico
