<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class StudyProgramHelper
{
  public static function getAsOptions(string $token, ?string $majorId = null): array
  {
    $queryParams = [
      'major_id' => $majorId
    ];

    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/study-programs/options');

    return $response->json();
  }
}
