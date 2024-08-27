<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckJwtValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Verifica si el token está presente
            $user =JWTAuth::parseToken();
            $request->merge(['userAuth' => $user->getPayload()->get()]);
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error' => 'invalid-token'],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error' => 'token-expired'],401);
            }else{
                return response()->json(['error' => 'token-not-found'],401);
            }

        }

        return $next($request);
    }
}
