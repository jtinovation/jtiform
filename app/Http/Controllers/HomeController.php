<?php

namespace App\Http\Controllers;

use App\Enums\FormTypeEnum;
use App\Helpers\ApiHelper;
use App\Helpers\FormHelper;
use App\Helpers\HomeHelper;
use App\Helpers\MajorApiHelper;
use App\Helpers\SessionApiHelper;
use App\Helpers\SubjectLectureApiHelper;
use App\Models\Form;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

  protected $apiHelper;
  protected $formHelper;

  public function __construct(ApiHelper $apiHelper, FormHelper $formHelper)
  {
    $this->apiHelper = $apiHelper;
    $this->formHelper = $formHelper;
  }


  public function index(Request $req)
  {
    $user = $this->apiHelper->getMe(Auth::user()->token);
    $activeForm = $this->formHelper->getFormVisibleToUser($user, null, false);

    $sessions = SessionApiHelper::getAsOptions(Auth::user()->token)['data'] ?? [];
    $majors = MajorApiHelper::getAsOptions(Auth::user()->token)['data'] ?? [];

    $kpis = null;
    $prodiChart = null;
    $questionStackChart = null;
    $lectureReportData = null;

    if (Auth::user()->hasAnyRole('superadmin|admin|direktur|wadir|kajur|kaprodi')) {
      $filter = [
        'session_id'       => $req->string('session_id')->toString() ?: (($last = Arr::last($sessions)) ? ($last['value'] ?? $last['id'] ?? null) : null),
        'is_even'          => $req->filled('is_even') ? (int) $req->input('is_even') : null, // 0/1
        'major_id'         => $req->string('major_id')->toString() ?: null,
        'study_program_id' => $req->string('study_program_id')->toString() ?: null,
        'type'             => $req->string('type')->toString() ?: null, // lecture_evaluation|general
        'status'           => $req->string('status')->toString() ?: null, // active|finished
      ];

      $kpis = HomeHelper::getKpis($filter);
      $prodiChart = HomeHelper::buildProdiChart($filter);
      $questionStackChart = HomeHelper::buildQuestionScoreStack($filter);
      $lectureReportData = HomeHelper::lectureReportData();
    }

    if (Auth::user()->hasAnyRole('lecturer')) {
      $lectureReportData = HomeHelper::lectureReportData();
    }

    return view('content.dashboard.dashboard-main', compact(
      'activeForm',
      'sessions',
      'majors',
      'kpis',
      'prodiChart',
      'questionStackChart',
      'lectureReportData'
    ));
  }
}
