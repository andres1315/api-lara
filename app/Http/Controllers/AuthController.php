<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
  public function conf(Request $request)
  {
    // Si la autenticación es exitosa, devuelve un token con la configuración de la base de datos
    $dbConfig = session('db_config');

    return response()->json([
      'success' => true,
      'db_config' => $dbConfig // Devuelve la configuración de la base de datos encriptada
    ]);
  }

  public function login(Request $request)
  {
    $credentials = [
      'usuarioid' => strval($request->input('user')),
      'password' => strval($request->input('password'))
    ];

    $user = User::where('usuarioid', $credentials['usuarioid'])->where('estado','A')->first();

    if (!$user || !$user->validateForPassportPasswordGrant($credentials['password'])) {
      return response()->json(['message' => 'Usuario o Contraseña Incorrecta', 'success' => false, 'data' => $user], 401);
    }


    try {
      if (!$token = JWTAuth::fromUser($user)) {
        return response()->json(['message' => 'No autorizado', 'success' => false], 401);
      }
    } catch (JWTException $e) {
      return response()->json(['message' => 'Could not create token' . $e, 'success' => 'false'], 500);
    }

    return response()->json([
      'token' => $token,
      'expires_in' => JWTAuth::factory()->getTTL() * 60,
      'message' => '',
      'success' => true,
      'user' => $user
    ]);
  }
}
