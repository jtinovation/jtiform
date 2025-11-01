<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{

  public function showLoginForm()
  {
    return view('landing.index');
  }


  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required|min:6',
    ]);
    $credentials = $request->only('email', 'password');

    try {
      if (! $token = JWTAuth::attempt($credentials)) {
        return back()->withErrors(['login_error' => 'Email atau password salah']);
      }
    } catch (JWTException $e) {
      return back()->withErrors(['login_error' => 'Gagal membuat token']);
    }


    return redirect('/dashboard')->withCookie(cookie('jwt_token', $token, 60, null, null, false, true));
  }


  public function logout()
  {
    $cookie = cookie()->forget('jwt_token');
    return redirect('/login')->withCookie($cookie);
  }
}
