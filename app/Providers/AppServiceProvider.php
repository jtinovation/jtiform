<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Blade::if('role', function (string $roles, string $mode = 'any') {
      if (!Auth::check()) return false;
      return Auth::user()->matchesRoles($roles, $mode);
    });

    Blade::if('notrole', function (string $roles, string $mode = 'any') {
      if (!Auth::check()) return true;
      return !Auth::user()->matchesRoles($roles, $mode);
    });
  }
}
