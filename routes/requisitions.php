<?php

use App\Http\Controllers\RequisicionController;
use Illuminate\Support\Facades\Route;


Route::get('/', [RequisicionController::class, 'index'])->name('all-requisition');  // Lista todos las RQ
/* Route::get('/{id}', [UserController::class, 'show']); // Muestra un usuario específico */

