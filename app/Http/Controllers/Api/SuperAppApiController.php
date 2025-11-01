<?php

namespace App\Http\Controllers\Api;

use App\Dto\Api\GetStudentDto;
use App\Helpers\MajorApiHelper;
use App\Helpers\SessionApiHelper;
use App\Helpers\StudyProgramHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SuperAppApiController extends Controller
{
  public function majorOption()
  {
    $user = Auth::user();
    $majors = MajorApiHelper::getAsOptions($user->token);

    if ($user->hasAnyRole('kajur|kaprodi')) {
      if (!empty($user->major_id)) {
        $majors['data'] = array_values(array_filter(
          $majors['data'],
          function ($major) use ($user) {
            return $major['value'] == $user->major_id;
          }
        ));
      }
    }

    return $majors;
  }

  public function studyProgramOption(Request $request)
  {
    $majorId = $request->input('major_id');
    $user = Auth::user();
    $studyPrograms = StudyProgramHelper::getAsOptions($user->token, $majorId);

    if ($user->hasAnyRole('kaprodi')) {
      if (!empty($user->study_program_id)) {
        $studyPrograms['data'] = array_values(array_filter(
          $studyPrograms['data'],
          function ($studyProgram) use ($user) {
            return $studyProgram['value'] == $user->study_program_id;
          }
        ));
      }
    }

    return $studyPrograms;
  }

  public function studentOption(Request $request)
  {
    $studyProgramId = $request->input('study_program_id');
    $searchQuery = $request->input('q');

    $queryParams = [
      'filter' => json_encode([
        'study_program_id' => $studyProgramId
      ]),
      'search' => $searchQuery
    ];

    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . Auth::user()->token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/students');

    $studentsAsLabelValue = array_map(function ($student) {
      return [
        'label' => $student['nim'] . ' - ' . $student['name'],
        'value' => $student['user_id']
      ];
    }, $response->json()['data']);

    $studentsAsOptions = [
      'message' => $response->json()['message'],
      'data' => $studentsAsLabelValue
    ];

    return response()->json($studentsAsOptions);
  }

  public function staffOption(Request $request)
  {
    $majorId = $request->input('major_id');
    $searchQuery = $request->input('q');

    $queryParams = [
      'search' => $searchQuery,
      'major_id' => $majorId
    ];

    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . Auth::user()->token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/employees');

    $employeesAsLabelValue = array_map(function ($employee) {
      return [
        'label' => $employee['nip'] . ' - ' . $employee['name'],
        'value' => $employee['user_id']
      ];
    }, $response->json()['data']);

    $response = [
      'message' => $response->json()['message'],
      'data' => $employeesAsLabelValue
    ];

    return response()->json($response);
  }

  public function sessionOption()
  {
    $sessions = SessionApiHelper::getAsOptions(Auth::user()->token);
    return $sessions;
  }
}
