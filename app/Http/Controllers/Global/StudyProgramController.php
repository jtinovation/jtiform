<?php

namespace App\Http\Controllers\Global;

use App\Helpers\MajorApiHelper;
use App\Helpers\SemesterApiHelper;
use App\Helpers\SubjectLectureApiHelper;
use App\Http\Controllers\Controller;
use App\Jobs\Report\GenerateAggregatedReports;
use App\Models\ReportProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudyProgramController extends Controller
{
  public function reportProgram(Request $request)
  {
    $majorOptions = MajorApiHelper::getAsOptions(Auth::user()->token)['data'] ?? [];

    return view('content.study-program.evaluation', compact('majorOptions'));
  }

  public function getDataReportProgram(Request $request)
  {
    $majorId = $request->input('major_id');
    $studyProgramId = $request->input('study_program_id');
    $sessionId = $request->input('session_id');
    $semester = $request->input('semester');

    $query = ReportProdi::query()
      ->join('m_question', 'm_question.id', '=', 'prodi_report.m_question_id')
      ->select('prodi_report.*', 'm_question.question', 'm_question.sequence')
      ->where('m_study_program_id', $studyProgramId)
      ->where('m_major_id', $majorId)
      ->where('m_session_id', $sessionId)
      ->where('is_even', $semester == 1 ? 0 : 1)
      ->orderBy('m_question.sequence', 'asc')
      ->get();

    return response()->json(
      [
        'message' => 'ok',
        'data' => [
          'major_id' => $majorId,
          'study_program_id' => $studyProgramId,
          'session_id' => $sessionId,
          'semester' => $semester,
          'results' => $query->toArray(),
        ]
      ]
    );
  }

  public function generateData(Request $request)
  {
    $majorId = $request->input('major_id');
    $studyProgramId = $request->input('study_program_id');
    $sessionId = $request->input('session_id');
    $semester = $request->input('semester');

    $semesters = SemesterApiHelper::getSemesterAsOption(Auth::user()->token, $sessionId);

    if ($semester == 1) {
      $semesters = array_filter($semesters, function ($item) {
        return $item['semester'] % 2 == 1;
      });
    } elseif ($semester == 2) {
      $semesters = array_filter($semesters, function ($item) {
        return $item['semester'] % 2 == 0;
      });
    }

    $semesterIds = Arr::pluck($semesters, 'id');

    dispatch(new GenerateAggregatedReports(
      $studyProgramId,
      $majorId,
      $sessionId,
      $semesterIds,
      $semester == 1 ? 0 : 1
    ))->onQueue('generate-report-prodi');

    return response()->json([
      'message' => 'Report generation job dispatched.',
    ]);
  }

  public function regenerateData(Request $request)
  {
    $majorId = $request->input('major_id');
    $studyProgramId = $request->input('study_program_id');
    $sessionId = $request->input('session_id');
    $semester = $request->input('semester');

    ReportProdi::query()
      ->where('m_study_program_id', $studyProgramId)
      ->where('m_major_id', $majorId)
      ->where('m_session_id', $sessionId)
      ->where('is_even', $semester == 1 ? 0 : 1)
      ->delete();

    $semesters = SemesterApiHelper::getSemesterAsOption(Auth::user()->token, $sessionId);

    if ($semester == 1) {
      $semesters = array_filter($semesters, function ($item) {
        return $item['semester'] % 2 == 1;
      });
    } elseif ($semester == 2) {
      $semesters = array_filter($semesters, function ($item) {
        return $item['semester'] % 2 == 0;
      });
    }

    $semesterIds = Arr::pluck($semesters, 'id');

    dispatch(new GenerateAggregatedReports(
      $studyProgramId,
      $majorId,
      $sessionId,
      $semesterIds,
      $semester == 1 ? 0 : 1
    ))->onQueue('generate-report-prodi');

    return response()->json([
      'message' => 'Report regeneration job dispatched.',
    ]);
  }
}
