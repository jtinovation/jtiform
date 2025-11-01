<?php

namespace App\Http\Controllers\Global;

use App\Enums\FormTypeEnum;
use App\Helpers\MajorApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('search');
    $majorId = $request->input('major_id');
    $studyProgramId = $request->input('study_program_id');
    $user = Auth::user();
    $majors = MajorApiHelper::getAsOptions($user->token)['data'] ?? [];

    $forms = Form::query()
      ->whereHas('reports', function ($query) use ($user, $majorId, $studyProgramId) {
        $query
          ->when($user->hasRole('kajur'), function ($query) use ($user) {
            $query->where('m_major_id_employee', $user->major_id);
          })
          ->when($user->hasRole('kaprodi'), function ($query) use ($user) {
            $query->where('m_study_program_id_employee', $user->study_program_id);
          })
          ->when($majorId && $user->hasAnyRole('superadmin|admin|direktur|wadir'), function ($query) use ($majorId) {
            $query->where('m_major_id_employee', $majorId);
          })
          ->when($studyProgramId && $user->hasAnyRole('superadmin|admin|direktur|wadir'), function ($query) use ($studyProgramId) {
            $query->where('m_study_program_id_employee', $studyProgramId);
          });
      })
      ->when($search, function ($query, $search) {
        $query->where(function ($subQuery) use ($search) {
          $subQuery->where('title', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');
        });
      })
      ->withCount('submissions as total_respondents')
      ->where('type', FormTypeEnum::LECTURE_EVALUATION->value)
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->withQueryString();

    return view('content.evaluation.index', compact('forms', 'majors', 'majorId', 'studyProgramId'));
  }
}
