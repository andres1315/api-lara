<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetClientDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nit = $request->header('NIT');

        if (!$nit) {
            return response()->json(['error' => 'NIT is required'], 400);
        }

        $client = DB::connection('sqlsrv')->table('configclient')->where('nit', $nit)->first();
        var_dump($client);
        if (!$client) {
            return response()->json(['error' => 'Invalid NIT'], 404);
        }

        config()->set('database.connections.client', [
            'driver'    => 'sqlsrv',
            'host'      => $client->host,
            'database'  => $client->database_name,
            'username'  => $client->username,
            'password'  => $client->password,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
        ]);

        DB::setDefaultConnection('client');

         // Guarda los datos de la base de datos en la sesión o en un token encriptado
         session(['db_config' => encrypt(json_encode([
            'host' => $client->host,
            'database' => $client->database_name,
            'username' => $client->username,
            'password' => $client->password,
        ]))]);


        return $next($request);
    }
}
