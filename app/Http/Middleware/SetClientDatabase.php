<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
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
        $nit = (string)$request->post('NIT');
        if (!$nit) {
            return response()->json(['error' => 'NIT is required'], 400);
        }





        try{
            $client=  DB::connection('sqlsrv')
            ->table('ModuloAcceso as MA')
            ->select(
                'C.Nombre as name_client',
                'AC.HostDB as host',
                'AC.BaseDatos as database_name',
                'AC.Usuario as username',
                'AC.Clave as password',
                'AC.DriverDB as driver'
            )
            ->leftJoin('Modulo as M', 'MA.ModuloId', '=', 'M.ModuloId')
            ->leftJoin('AplicativoCliente as AC', 'MA.AplicativoClienteId', '=', 'AC.AplicativoClienteId')
            ->leftJoin('Aplicativo as A', 'AC.AplicativoId', '=', 'A.AplicativoId')
            ->leftJoin('Cliente as C', 'AC.ClienteId', '=', 'C.ClienteId')
            ->where('C.Nit', $nit)
            ->where('C.Estado', 'A')
            ->where('A.Estado', 'A')
            ->where('M.ModuloId', 'WEBCLUB')
            ->where('A.AplicativoId', 'WEBCLUB')
            ->where('AC.Estado', 'A')
            ->first();

            if (!$client) {
                return response()->json(['message' => 'El numero ingresado no coincide con los registrados en el sistema o se encuentra en estado Inactiva.','success'=>'false'], 400);
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
                'strict'    => false,
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


        }catch(Exception $e){
            return response()->json(['error'=> $e->getMessage()],404);
        }
        return $next($request);

    }
}
