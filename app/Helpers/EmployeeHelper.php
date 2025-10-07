<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class EmployeeHelper
{
  public static function getEmployeeAsOptions(string $token, string $majorId, string $studyProgramId, string $position): array
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters([
        'major_id' => $majorId,
        'study_program_id' => $studyProgramId,
        'position' => $position,
      ])
      ->get(config('app.super_app_url') . '/employees/options');

    return $response->json()['data'] ?? null;
  }
}
