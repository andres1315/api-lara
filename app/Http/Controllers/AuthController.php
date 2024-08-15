<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function login(Request $request)
     {
         // Lógica de autenticación
 
         // Si la autenticación es exitosa, devuelve un token con la configuración de la base de datos
         $dbConfig = session('db_config');
 
         return response()->json([
             'token' => Auth::user()->createToken('API Token')->plainTextToken,
             'db_config' => $dbConfig // Devuelve la configuración de la base de datos encriptada
         ]);
     }

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
