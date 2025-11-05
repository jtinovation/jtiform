<?php

namespace App\Http\Controllers\Global;

use App\Helpers\MajorApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
  public function index(Request $request)
  {
    $user = Auth::user();
    $majorOptions = MajorApiHelper::getAsOptions($user->token)['data'] ?? [];

    if ($user->hasAnyRole('kajur|kaprodi')) {
      $majorOptions = array_filter($majorOptions, function ($major) use ($user) {
        return $major['value'] == $user->major_id;
      });
    }
    return view('content.subject.index', compact('majorOptions'));
  }

  public function getDataReportSubject(Request $request)
  {
    $majorId = $request->input('major_id');
    $studyProgramId = $request->input('study_program_id');
    $sessionId = $request->input('session_id');
    $semester = $request->input('semester');

    $isEven = $semester == 1 ? 0 : 1;

    $fromRaw = "m_report r
                JOIN m_form mf ON mf.id = r.m_form_id
                JOIN JSON_TABLE(
                    r.report_details,
                    '$[*]' COLUMNS(
                        course_code VARCHAR(32)              PATH '$.course_code',
                        course_name VARCHAR(255)             PATH '$.course_name',
                        respondents INT                      PATH '$.respondents',
                        average_score DECIMAL(7,2)           PATH '$.average_score',
                        major_id_subject CHAR(36)            PATH '$.major_id_subject',
                        study_program_id_subject CHAR(36)    PATH '$.study_program_id_subject'
                    )
                ) jt";

    $groupedQuery = DB::query()
      ->fromRaw($fromRaw)
      ->where('mf.session_id', $sessionId)
      ->where('mf.is_even', $isEven)
      ->whereRaw('jt.major_id_subject = ?', [$majorId])
      ->whereRaw('jt.study_program_id_subject = ?', [$studyProgramId])
      ->selectRaw('
            jt.course_code  AS code,
            jt.course_name  AS name,
            SUM(jt.respondents) AS respondent_total,
            ROUND(SUM(jt.average_score * jt.respondents) / NULLIF(SUM(jt.respondents), 0), 2) AS avg_score
        ')
      ->groupBy('jt.course_code', 'jt.course_name')
      ->orderByDesc('avg_score');

    $grouped = $groupedQuery->paginate(10)->through(fn($row) => [
      'code'             => $row->code,
      'name'             => $row->name,
      'respondent_total' => (int) $row->respondent_total,
      'avg_score'        => (float) $row->avg_score,
    ]);

    return response()->json(
      [
        'message' => 'ok',
        'data' => [
          'major_id' => $majorId,
          'study_program_id' => $studyProgramId,
          'session_id' => $sessionId,
          'semester' => $semester,
          'results' => $grouped->items(),
        ],
        'meta' => [
          'first_item' => $grouped->firstItem(),
          'last_item' => $grouped->lastItem(),
          'current_page' => $grouped->currentPage(),
          'last_page' => $grouped->lastPage(),
          'per_page' => $grouped->perPage(),
          'total' => $grouped->total(),
          'links' => $grouped->links('vendor.pagination.bootstrap-5')->toHtml(),
        ]
      ]
    );
  }
}
