<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SubjectLectureApiHelper
{
  public static function getSubjectLectures(string $token, array $queryParams, int $page = 1, int $perPage = 500): ?array
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters([
        'page'      => $page,
        'per_page'   => $perPage,
        'filter'     => json_encode($queryParams),
      ])
      ->get(config('app.super_app_url') . '/subject-lectures');

    return $response->json();
  }
}
