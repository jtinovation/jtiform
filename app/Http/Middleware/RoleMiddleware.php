<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next, string $spec = ''): Response
  {
    if (!Auth::check()) {
      abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
    }

    $user = Auth::user();

    $mode = 'any'; // any|all
    $rolesExpr = $spec;

    // Dukung "role:all,admin|editor" atau "role:any,admin|editor"
    if (str_contains($spec, ',')) {
      [$maybeMode, $rest] = array_pad(explode(',', $spec, 2), 2, '');
      $maybeMode = strtolower(trim($maybeMode));
      if (in_array($maybeMode, ['any', 'all'], true)) {
        $mode = $maybeMode;
        $rolesExpr = $rest;
      }
    }

    // Daftar role dipisah dengan '|'
    $required = array_filter(array_map(
      fn($r) => strtolower(trim($r)),
      explode('|', $rolesExpr)
    ));

    if (empty($required)) {
      // Jika tidak ada role yang dispesifikkan, izinkan (atau bisa juga abort 500)
      return $next($request);
    }

    $authorized = $mode === 'all'
      ? $user->hasAllRoles($required)
      : $user->hasAnyRole($required);

    if (!$authorized) {
      abort(Response::HTTP_FORBIDDEN, 'This action is unauthorized.');
    }

    return $next($request);
  }
}
