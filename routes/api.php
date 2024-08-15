<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\SetClientDatabase;
use App\Http\Middleware\SetDatabaseFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/auth', [AuthController::class, 'conf'])->middleware(SetClientDatabase::class);
Route::post('/login', [AuthController::class, 'login'])->middleware(SetDatabaseFromHeader::class);

Route::middleware([SetDatabaseFromHeader::class])->group(function () {
    require __DIR__.'/user.php';
});


Route::get('/{any}', function () {
    return response()->view('errors.404', [], 404);
})->where('any', '.*');
