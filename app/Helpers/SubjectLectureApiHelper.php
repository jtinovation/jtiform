<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SubjectLectureApiHelper
{
  public function getSubjectLectures(string $token, array $queryParams): ?array
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters([
        'per_page'   => 500,
        'filter'     => json_encode($queryParams),
      ])
      ->get(config('app.super_app_url') . '/subject-lectures');

    return $response->json()['data'] ?? null;
  }
}
