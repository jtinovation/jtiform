<?php

namespace App\Http\Controllers\Lecture;

use App\Http\Controllers\Controller;
use App\Models\Report;
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
    $reports = Report::where('m_form_id', $formId)
      ->with([
        'form.questions' => function ($query) {
          $query->orderBy('sequence', 'asc');
        },
        'user'
      ])
      ->get();

    $pdf = Pdf::loadView('content.lecture.evaluation.report-pdf-all', compact('reports'));
    return $pdf->setPaper('a4', 'potrait')->stream('rapor-evaluasi-' . $reports->first()->form->code . '.pdf');
  }
}
