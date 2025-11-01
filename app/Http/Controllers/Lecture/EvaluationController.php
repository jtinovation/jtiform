<?php

namespace App\Http\Controllers\Lecture;

use App\Helpers\ApiHelper;
use App\Helpers\EmployeeHelper;
use App\Helpers\EvaluationHelper;
use App\Helpers\HomeHelper;
use App\Helpers\MajorApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluationController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('search');
    $majorId = $request->input('major_id');
    $studyProgramId = $request->input('study_program_id');

    $user = Auth::user();
    $majors = MajorApiHelper::getAsOptions($user->token)['data'] ?? [];

    if ($user->hasAnyRole('kajur|kaprodi')) {
      $majors = array_filter($majors, function ($major) use ($user) {
        return $major['value'] == $user->major_id;
      });
    }

    $lectures = User::query()
      ->whereJsonContains('roles', 'lecturer')
      ->withAvg('reports as avg_score', 'overall_average_score')
      ->when($search, function ($query, $search) {
        $query->where(function ($subQuery) use ($search) {
          $subQuery->where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%');
        });
      })
      ->when($user->hasRole('kajur'), function ($query) use ($user) {
        $query->where('major_id', $user->major_id);
      })
      ->when($user->hasRole('kaprodi'), function ($query) use ($user) {
        $query->where('major_id', $user->major_id)
          ->where('study_program_id', $user->study_program_id);
      })
      ->when($majorId, function ($query, $majorId) {
        $query->where('major_id', $majorId);
      })
      ->when($studyProgramId, function ($query, $studyProgramId) {
        $query->where('study_program_id', $studyProgramId);
      })
      ->orderBy('name', 'asc')
      ->paginate(10);

    return view('content.lecture.evaluation.index', compact('lectures', 'majors'));
  }

  public function show($userId, Request $request)
  {
    $search = $request->input('search');

    $user = User::where('id', $userId)
      ->whereJsonContains('roles', 'lecturer')
      ->withAvg('reports as avg_score', 'overall_average_score')
      ->firstOrFail();

    $lectureReportData = HomeHelper::lectureReportData(userId: $user->id);

    $reports = EvaluationHelper::getEvaluationReports($user->id, $request);
    return view('content.lecture.evaluation.show', compact('user', 'lectureReportData', 'reports'));
  }

  public function my(Request $request)
  {
    $user = Auth::user();

    $reports = EvaluationHelper::getEvaluationReports($user->id, $request);

    return view('content.lecture.evaluation.my.index', compact('reports'));
  }

  public function generateReportPdf($id)
  {
    $report = Report::where('id', $id)
      ->with([
        'form.questions' => function ($query) {
          $query->orderBy('sequence', 'asc');
        },
        'user'
      ])
      ->firstOrFail();

    $pdf = Pdf::loadView('content.lecture.evaluation.report-pdf', compact('report'));
    return $pdf->setPaper('a4', 'potrait')->stream('rapor-evaluasi-' . $report->form->code . '.pdf');
  }

  public function generateReportPdfAll(Request $request, $formId)
  {
    $majorId = $request->input('major_id');
    $studyProgramId = $request->input('study_program_id');

    if (Auth::user()->hasRole('kaprodi')) {
      $userDetails = ApiHelper::getMe(Auth::user()->token);
      $employees = EmployeeHelper::getEmployeeAsOptions(
        Auth::user()->token,
        $userDetails['employee_detail']['m_major_id'],
        $userDetails['employee_detail']['m_study_program_id'],
        'DOSEN'
      );
    } elseif ($majorId && $studyProgramId) {
      $employees = EmployeeHelper::getEmployeeAsOptions(
        Auth::user()->token,
        $majorId,
        $studyProgramId,
        'DOSEN'
      );
    } else {
      $employees = null;
    }

    $reports = Report::query()
      ->with([
        'form.questions' => function ($query) {
          $query->orderBy('sequence', 'asc');
        },
        'user'
      ])
      ->when($employees, function ($query) use ($employees) {
        $userIds = collect($employees)->pluck('value')->toArray();
        $query->whereIn('m_employee_id', $userIds);
      })
      ->where('m_form_id', $formId)
      ->get();

    $reports = $reports->sortBy(function ($report) {
      return $report->user->name ?? '';
    })->values();

    $pdf = Pdf::loadView('content.lecture.evaluation.report-pdf-all', compact('reports', 'employees'));
    return $pdf->setPaper('a4', 'potrait')->stream('rapor-evaluasi-' . $reports->first()->form->code . '.pdf');
  }
}
