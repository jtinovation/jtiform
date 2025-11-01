<?php

namespace App\Helpers;

class GlobalHelper
{
  public static function trendBadgeDashboard($pct)
  {
    if (is_null($pct)) {
      return '<span class="badge bg-label-secondary">–</span>';
    }
    $icon = $pct >= 0 ? 'icon-base ri ri-arrow-up-s-line' : 'icon-base ri ri-arrow-down-s-line';
    $cls = $pct >= 0 ? 'bg-label-success' : 'bg-label-danger';
    return '<span class="badge ' .
      $cls .
      ' d-inline-flex align-items-center gap-1"><i class="' .
      $icon .
      '"></i> ' .
      number_format(abs($pct), 1) .
      '%</span>';
  }
}
