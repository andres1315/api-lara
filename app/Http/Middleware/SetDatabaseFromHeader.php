<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetDatabaseFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $encryptedDbConfig = $request->header('DB-CONFIG');

        if (!$encryptedDbConfig) {
            return response()->json(['error' => 'Database configuration is required'], 400);
        }

        try {
            $dbConfig = json_decode(Crypt::decrypt($encryptedDbConfig), true);

            config()->set('database.connections.client', [
                'driver'    => 'sqlsrv',
                'host'      => $dbConfig['host'],
                'database'  => $dbConfig['database'],
                'username'  => $dbConfig['username'],
                'password'  => $dbConfig['password'],
                'charset'   => 'utf8',
                'prefix'    => '',
            ]);

            DB::setDefaultConnection('client');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid database configuration'], 400);
        }

        return $next($request);
    }
}
