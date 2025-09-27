<?php

namespace App\Enums;

enum FormTypeEnum: string
{
  case GENERAL = 'general';
  case LECTURE_EVALUATION = 'lecture_evaluation';

  public static function toArray(): array
  {
    return array_map(fn($case) => $case->value, self::cases());
  }
}
