<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->cookie('jwt_token');

            if (! $token) {
                return redirect('/login')->withErrors(['auth_error' => 'Token tidak ditemukan, silakan login kembali.']);
            }

            $user = JWTAuth::setToken($token)->authenticate();

            if (! $user) {
                return redirect('/login')->withErrors(['auth_error' => 'User tidak valid']);
            }

           auth()->setUser($user);

         

        } catch (JWTException $e) {
            return redirect('/login')->withErrors(['auth_error' => 'Token tidak valid atau kadaluarsa']);
        }

        return $next($request);
    }
}
