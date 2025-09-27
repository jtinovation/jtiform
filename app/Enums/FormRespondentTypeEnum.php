<?php

namespace App\Enums;

enum FormRespondentTypeEnum: string
{
  case ALL = 'all';
  case MAJOR = 'major';
  case STUDY_PROGRAM = 'study_program';
  case STUDENT = 'student';
  case LECTURER = 'lecturer';
  case EDUCATIONAL_STAFF = 'educational_staff';

  public static function toArray(): array
  {
    return array_map(fn($case) => $case->value, self::cases());
  }
}
