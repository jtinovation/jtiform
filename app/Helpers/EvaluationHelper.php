<?php

namespace App\Helpers;

use App\Models\Report;
use Illuminate\Http\Request;

class EvaluationHelper
{
  public static function getEvaluationReports(string $userId, Request $request): \Illuminate\Pagination\LengthAwarePaginator
  {
    $search = $request->input('search');
    $reports = Report::where('m_user_id', $userId)
      ->with('form')
      ->when($search, function ($query, $search) {
        $query->whereHas('form', function ($q) use ($search) {
          $q->where(function ($subQuery) use ($search) {
            $subQuery->where('title', 'like', '%' . $search . '%')
              ->orWhere('code', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
          });
        });
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->withQueryString();

    return $reports;
  }
}
