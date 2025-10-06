<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SemesterApiHelper
{
  public function getSemesterAsOption(string $token, string $sessionID): ?array
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters([
        'session_id' => $sessionID,
      ])
      ->get(config('app.super_app_url') . '/semesters/options');

    return $response->json()['data'] ?? null;
  }
}
