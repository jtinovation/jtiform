<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class ApiHelper
{
  public function getMe(string $token): ?array
  {
    $cacheKey = 'me_' . md5($token);
    $ttl = 300; // seconds

    $cached = Redis::get($cacheKey);
    if ($cached) {
      return json_decode($cached, true);
    }

    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->get(config('app.super_app_url') . '/auth/me');

    if ($response->failed()) {
      return null;
    }

    $data = $response->json()['data'] ?? null;
    if ($data) {
      Redis::setex($cacheKey, $ttl, json_encode($data));
    }

    return $data;
  }
}
