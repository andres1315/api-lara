<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
  /**
   * Display a listing of the resource.
   */

  public function conf(Request $request)
  {
    // Si la autenticación es exitosa, devuelve un token con la configuración de la base de datos
    $dbConfig = session('db_config');

    return response()->json([
      'token' => "Auth::user()->createToken('API Token')->plainTextToken",
      'success' =>true,
      'db_config' => $dbConfig // Devuelve la configuración de la base de datos encriptada
    ]);
  }

 public function login(Request $request){
  $credentials = $request->only('user','password');

  try {
    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['message' => 'Unauthorized', 'success' => 'false'], 401);
    }
  } catch (JWTException $e) {
      return response()->json(['message' => 'Could not create token', 'success' => 'false'], 500);
  }

  return response()->json(compact('token'));
 }
}
