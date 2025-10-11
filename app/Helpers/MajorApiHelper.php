<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class MajorApiHelper
{
  public static function getAsOptions(string $token)
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->get(config('app.super_app_url') . '/majors/options');

    return $response->json();
  }
}
