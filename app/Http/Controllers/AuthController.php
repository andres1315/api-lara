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
      'token' => "Auth::user()->createToken('API Token')->plainTextToken",
      'success' => true,
      'db_config' => $dbConfig // Devuelve la configuración de la base de datos encriptada
    ]);
  }

  public function login(Request $request)
  {
    $credentials = [
      'usuarioid' => $request->input('user'),
      'password' => $request->input('password')
    ];

    $user = User::where('usuarioid', $credentials['usuarioid'])->first();

    if (!$user || !$user->validateForPassportPasswordGrant($credentials['password'])) {
      return response()->json(['message' => 'Unauthorized', 'success' => false, 'data' => $user], 401);
    }


    try {
      if (!$token = JWTAuth::fromUser($user)) {
        return response()->json(['message' => 'Unauthorized', 'success' => false], 401);
      }
    } catch (JWTException $e) {
      return response()->json(['message' => 'Could not create token' . $e, 'success' => 'false'], 500);
    }
    $userData = ['id' => $user->usuarioId, 'name' => $user->nombre, 'document' => $user->cedula];
    return response()->json([
      'token' => $token,
      'message' => '',
      'success' => true,
      'user' => $userData
    ]);
  }
}
