<?php

use App\Http\Middleware\CheckJwtValid;
use App\Http\Middleware\SetClientDatabase;
use App\Http\Middleware\SetDatabaseFromHeader;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then:function(){
            Route::middleware(['auth-custom'])
            ->prefix('/api/user')
            ->group(base_path('routes/user.php'));

            Route::middleware(['auth-custom'])
            ->prefix('/api/requ')
            ->group(base_path('routes/requisitions.php'));

            Route::middleware(['auth-custom'])
            ->prefix('/api/products')
            ->group(base_path('routes/products.php'));

            Route::match(['get', 'post', 'put', 'delete'], '/{any}', function () {
                return response()->view('errors.404', [], 404);
            })->where('any', '.*');

        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'databaseHeader' => SetDatabaseFromHeader::class,
            'setDatabase' => SetClientDatabase::class,
            'check-jwt' =>CheckJwtValid::class,
        ]);

        $middleware->appendToGroup('auth-custom', [
            SetDatabaseFromHeader::class,
            CheckJwtValid::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
