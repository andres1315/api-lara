<?php

use App\Http\Middleware\SetClientDatabase;
use App\Http\Middleware\SetDatabaseFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/* Route::post('/login', [AuthController::class, 'login'])->middleware(SetClientDatabase::class); */

Route::middleware([SetDatabaseFromHeader::class])->group(function () {
    require __DIR__.'/user.php';
});