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

  public function label(): string
  {
    return match ($this) {
      self::ALL => 'Semua',
      self::MAJOR => 'Jurusan',
      self::STUDY_PROGRAM => 'Program Studi',
      self::STUDENT => 'Mahasiswa',
      self::LECTURER => 'Dosen',
      self::EDUCATIONAL_STAFF => 'Staf Pendidikan',
    };
  }
}
