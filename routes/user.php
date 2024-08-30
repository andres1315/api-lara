<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])->name('all-users');  // Lista todos los usuarios
Route::get('/{id}', [UserController::class, 'show']); // Muestra un usuario específico
/* Route::post('/', [UserController::class, 'store'])->name('me'); // Crea un nuevo usuario
Route::put('/{id}', [UserController::class, 'update']); // Actualiza un usuario específico
Route::delete('/{id}', [UserController::class, 'destroy']); // Elimina un usuario específico
 */
