<?php

use App\Http\Controllers\RequisicionController;
use Illuminate\Support\Facades\Route;


Route::get('/', [RequisicionController::class, 'index'])->name('all-requisition');  // Lista todos las RQ
Route::get('/detail', [RequisicionController::class, 'detailGroup'])->name('detail-requisition-group');  // Lista detalle RQ Grupo
Route::get('/tofinish', [RequisicionController::class, 'toFinishRequisition'])->name('tofinish-requisition');  // Finalizar Requisition luego de que se pickean todos los productos
Route::get('/{id}', [RequisicionController::class, 'show'])->name('detail-requisition');  // Lista detalle RQ
Route::post('/newgroup', [RequisicionController::class, 'createGroupRequisition'])->name('newgroup-requisition');  // Crear grupo de rq
Route::post('/assignbasket', [RequisicionController::class, 'toAssignBasket'])->name('assign-requisition');  // Asignar Canasta



