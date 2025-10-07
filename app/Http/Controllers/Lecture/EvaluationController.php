<?php

namespace App\Http\Controllers\Lecture;

use App\Helpers\ApiHelper;
use App\Helpers\EmployeeHelper;
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
    $reports = Report::where('m_user_id', Auth::user()->id)
      ->with('form')
      ->when($search, function ($query, $search) {
        $query->whereHas('form', function ($q) use ($search) {
          $q->where(function ($subQuery) use ($search) {
            $subQuery->where('title', 'like', '%' . $search . '%')
              ->orWhere('code', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
          });
        });
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->withQueryString();


    return view('content.lecture.evaluation.index', compact('reports'));
  }

  public function generateReportPdf($id)
  {
    $report = Report::where('id', $id)
      ->where('m_user_id', Auth::user()->id)
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

  public function generateReportPdfAll($formId)
  {
    if (Auth::user()->hasRole('kaprodi')) {
      $userDetails = ApiHelper::getMe(Auth::user()->token);
      $employees = EmployeeHelper::getEmployeeAsOptions(
        Auth::user()->token,
        $userDetails['employee_detail']['m_major_id'],
        $userDetails['employee_detail']['m_study_program_id'],
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
