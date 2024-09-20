<?php

use App\Http\Controllers\RequisicionController;
use Illuminate\Support\Facades\Route;


Route::get('/', [RequisicionController::class, 'index'])->name('all-requisition');  // Lista todos las RQ
Route::get('/{id}', [RequisicionController::class, 'show'])->name('detail-requisition');  // Lista detalle RQ
Route::get('/tofinish/{id}', [RequisicionController::class, 'toFinishRequisition'])->name('tofinish-requisition');  // Finalizar Requisition luego de que se pickean todos los productos
Route::post('/newgroup', [RequisicionController::class, 'createGroupRequisition'])->name('newgroup-requisition');  // Finalizar Requisition luego de que se pickean todos los productos



