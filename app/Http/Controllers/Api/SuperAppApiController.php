<?php

namespace App\Http\Controllers\Api;

use App\Dto\Api\GetStudentDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SuperAppApiController extends Controller
{
  public function majorOption()
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . Auth::user()->token,
    ])
      ->get(config('app.super_app_url') . '/majors/options');

    return $response->json();
  }

  public function studyProgramOption(Request $request)
  {
    $majorId = $request->input('major_id');

    $queryParams = [
      'major_id' => $majorId
    ];

    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . Auth::user()->token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/study-programs/options');

    return $response->json();
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
}
