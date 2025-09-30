<?php

namespace App\Helpers;

use App\Enums\FormRespondentTypeEnum;
use App\Models\Form;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FormHelper
{
  public static function getFormVisibleToUser(array $user, ?string $search, bool $isFilled): LengthAwarePaginator
  {
    $loggedUser = Auth::user();
    $userId          = Arr::get($user, 'id');
    $userRoles       = Arr::get($user, 'roles', []);
    if (in_array('student', $userRoles, true)) {
      $userMajorId     = Arr::get($user, 'student_detail.m_major_id');
      $userStudyProgId = Arr::get($user, 'student_detail.m_study_program_id');
    } else {
      $userMajorId     = Arr::get($user, 'employee_detail.m_major_id');
      $userStudyProgId = Arr::get($user, 'employee_detail.m_study_program_id');
    }

    $isStudent  = in_array('student', $userRoles, true);
    $isLecturer = in_array('lecturer', $userRoles, true);
    $isStaff    = in_array('technician', $userRoles, true)
      || in_array('admin', $userRoles, true)
      || in_array('educational_staff', $userRoles, true);

    // ---- PREFILTER DI DB ----
    $prefiltered = Form::query()
      ->select(['id', 'code', 'type', 'cover_path', 'cover_file', 'title', 'description', 'start_at', 'end_at', 'respondents'])
      ->where('start_at', '<=', now())
      ->where('end_at', '>=', now())
      ->where(function ($q) use ($userId, $userMajorId, $userStudyProgId, $isStudent, $isLecturer, $isStaff) {
        // type = 'all'
        $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.type')) = 'all'");

        // type = 'major' & match major_id
        if ($userMajorId) {
          $q->orWhere(function ($q) use ($userMajorId) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.type')) = 'major'")
              ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.major_id')) = ?", [$userMajorId]);
          });
        }

        // type = 'study_program' & (match study_program_id | (study_program_id null & match major_id))
        if ($userStudyProgId || $userMajorId) {
          $q->orWhere(function ($q) use ($userStudyProgId, $userMajorId) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.type')) = 'study_program'")
              ->where(function ($q) use ($userStudyProgId, $userMajorId) {
                if ($userStudyProgId) {
                  $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.study_program_id')) = ?", [$userStudyProgId]);
                }
                if ($userMajorId) {
                  $q->orWhere(function ($q) use ($userMajorId) {
                    $q->whereRaw("JSON_EXTRACT(respondents, '$.study_program_id') IS NULL")
                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.major_id')) = ?", [$userMajorId]);
                  });
                }
              });
          });
        }

        // type = 'student'
        if ($isStudent) {
          $q->orWhere(function ($q) use ($userId, $userStudyProgId, $userMajorId) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.type')) = 'student'")
              ->where(function ($q) use ($userId, $userStudyProgId, $userMajorId) {
                // ids berisi userId
                $q->whereRaw("JSON_CONTAINS(JSON_EXTRACT(respondents, '$.respondent_ids'), JSON_QUOTE(?))", [$userId]);

                // atau match study_program_id
                if ($userStudyProgId) {
                  $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.study_program_id')) = ?", [$userStudyProgId]);
                }

                // atau match major_id (saat study_program_id tidak diisi)
                if ($userMajorId) {
                  $q->orWhere(function ($q) use ($userMajorId) {
                    $q->whereRaw("JSON_EXTRACT(respondents, '$.study_program_id') IS NULL")
                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.major_id')) = ?", [$userMajorId]);
                  });
                }

                // atau rule tanpa batasan (ids kosong & major/prodi null) → semua student
                $q->orWhereRaw("COALESCE(JSON_LENGTH(JSON_EXTRACT(respondents, '$.respondent_ids')), 0) = 0
                                          AND JSON_EXTRACT(respondents, '$.major_id') IS NULL
                                          AND JSON_EXTRACT(respondents, '$.study_program_id') IS NULL");
              });
          });
        }

        // type = 'lecturer'
        if ($isLecturer) {
          $q->orWhere(function ($q) use ($userId, $userStudyProgId, $userMajorId) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.type')) = 'lecturer'")
              ->where(function ($q) use ($userId, $userStudyProgId, $userMajorId) {
                $q->whereRaw("JSON_CONTAINS(JSON_EXTRACT(respondents, '$.respondent_ids'), JSON_QUOTE(?))", [$userId]);

                if ($userStudyProgId) {
                  $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.study_program_id')) = ?", [$userStudyProgId]);
                }
                if ($userMajorId) {
                  $q->orWhere(function ($q) use ($userMajorId) {
                    $q->whereRaw("JSON_EXTRACT(respondents, '$.study_program_id') IS NULL")
                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.major_id')) = ?", [$userMajorId]);
                  });
                }
                $q->orWhereRaw("COALESCE(JSON_LENGTH(JSON_EXTRACT(respondents, '$.respondent_ids')), 0) = 0
                                          AND JSON_EXTRACT(respondents, '$.major_id') IS NULL
                                          AND JSON_EXTRACT(respondents, '$.study_program_id') IS NULL");
              });
          });
        }

        // type = 'educational_staff'
        if ($isStaff) {
          $q->orWhere(function ($q) use ($userId, $userStudyProgId, $userMajorId) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.type')) = 'educational_staff'")
              ->where(function ($q) use ($userId, $userStudyProgId, $userMajorId) {
                $q->whereRaw("JSON_CONTAINS(JSON_EXTRACT(respondents, '$.respondent_ids'), JSON_QUOTE(?))", [$userId]);

                if ($userStudyProgId) {
                  $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.study_program_id')) = ?", [$userStudyProgId]);
                }
                if ($userMajorId) {
                  $q->orWhere(function ($q) use ($userMajorId) {
                    $q->whereRaw("JSON_EXTRACT(respondents, '$.study_program_id') IS NULL")
                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respondents, '$.major_id')) = ?", [$userMajorId]);
                  });
                }
                $q->orWhereRaw("COALESCE(JSON_LENGTH(JSON_EXTRACT(respondents, '$.respondent_ids')), 0) = 0
                                          AND JSON_EXTRACT(respondents, '$.major_id') IS NULL
                                          AND JSON_EXTRACT(respondents, '$.study_program_id') IS NULL");
              });
          });
        }
      })
      ->when($search, function ($q) use ($search) {
        $q->where(function ($q) use ($search) {
          $q->where('title', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->orWhere('code', 'like', '%' . $search . '%');
        });
      })
      ->when($isFilled, function ($q) use ($loggedUser) {
        $q->whereHas('submissions', function ($q) use ($loggedUser) {
          $q->where('m_user_id', $loggedUser->id);
        });
      }, function ($q) use ($loggedUser) {
        $q->whereDoesntHave('submissions', function ($q) use ($loggedUser) {
          $q->where('m_user_id', $loggedUser->id);
        });
      })
      ->latest()
      ->paginate(10)
      ->withQueryString();

    // ---- FINAL CHECK DI PHP (sama seperti logic sebelumnya) ----
    // $visibleForms = $prefiltered->filter(function (Form $form) use ($user, $userId, $userMajorId, $userStudyProgId, $isStudent, $isLecturer, $isStaff) {
    //   $r = $form->respondents;
    //   if (!is_array($r)) return false;

    //   $type            = $r['type']            ?? null;
    //   $ruleMajorId     = $r['major_id']        ?? null;
    //   $ruleStudyProgId = $r['study_program_id'] ?? null;
    //   $ids             = $r['respondent_ids']  ?? [];

    //   if (!is_array($ids) && !is_null($ids)) $ids = [$ids];
    //   $ids = array_map('strval', $ids);
    //   $eq  = fn($a, $b) => $a && $b && (string)$a === (string)$b;

    //   switch ($type) {
    //     case 'all':
    //       return true;
    //     case 'major':
    //       return $eq($userMajorId, $ruleMajorId);
    //     case 'study_program':
    //       if ($ruleStudyProgId) return $eq($userStudyProgId, $ruleStudyProgId);
    //       if ($ruleMajorId)     return $eq($userMajorId, $ruleMajorId);
    //       return false;
    //     case 'student':
    //       if (!$isStudent) return false;
    //       if (!empty($ids))      return in_array((string)$userId, $ids, true);
    //       if ($ruleStudyProgId)  return $eq($userStudyProgId, $ruleStudyProgId);
    //       if ($ruleMajorId)      return $eq($userMajorId, $ruleMajorId);
    //       return true;
    //     case 'lecturer':
    //       if (!$isLecturer) return false;
    //       if (!empty($ids))      return in_array((string)$userId, $ids, true);
    //       if ($ruleStudyProgId)  return $eq($userStudyProgId, $ruleStudyProgId);
    //       if ($ruleMajorId)      return $eq($userMajorId, $ruleMajorId);
    //       return true;
    //     case 'educational_staff':
    //       if (!$isStaff) return false;
    //       if (!empty($ids))      return in_array((string)$userId, $ids, true);
    //       if ($ruleStudyProgId)  return $eq($userStudyProgId, $ruleStudyProgId);
    //       if ($ruleMajorId)      return $eq($userMajorId, $ruleMajorId);
    //       return true;
    //     default:
    //       return false;
    //   }
    // })->values();

    return $prefiltered;
  }

  public static function getRespondentIds(array $validated): array
  {
    $respondents = [
      'type' => $validated['responden_type'],
    ];

    switch ($validated['responden_type']) {
      case FormRespondentTypeEnum::ALL->value:
        break;

      case FormRespondentTypeEnum::MAJOR->value:
        $respondents['major_id'] = $validated['major_id'];
        break;

      case FormRespondentTypeEnum::STUDY_PROGRAM->value:
        $respondents['major_id'] = $validated['major_id'];
        $respondents['study_program_id'] = $validated['study_program_id'];
        break;

      default:
        $hasMajor = isset($validated['major_id']) && $validated['major_id'];
        $hasStudy = isset($validated['study_program_id']);
        $hasIds   = isset($validated['respondent_ids']);

        if ($hasIds && $hasStudy && $hasMajor) {
          $respondents += [
            'major_id'         => $validated['major_id'],
            'study_program_id' => $validated['study_program_id'],
            'respondent_ids'   => $validated['respondent_ids'],
          ];
        } elseif ($hasStudy && $hasMajor) {
          $respondents += [
            'major_id'         => $validated['major_id'],
            'study_program_id' => $validated['study_program_id'],
          ];
        } elseif ($hasMajor) {
          $respondents['major_id'] = $validated['major_id'];
        } else {
          $respondents['respondent_ids'] = $validated['respondent_ids'];
        }
        break;
    }

    return $respondents;
  }
}
