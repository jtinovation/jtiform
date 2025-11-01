<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Helpers\FormHelper;
use App\Helpers\HomeHelper;
use App\Helpers\MajorApiHelper;
use App\Helpers\SessionApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

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
    $reports = null;
    $submissions = null;

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

    $user = Auth::user();
    if (Auth::user()->hasAnyRole('lecturer') && Auth::user()->hasNoRole('direktur|wadir|kajur|kaprodi')) {
      $lectureReportData = HomeHelper::lectureReportData();
    }

    if (Auth::user()->hasAnyRole('kajur|kaprodi')) {
      $majors = array_filter($majors, function ($major) use ($user) {
        return $major['value'] == $user->major_id;
      });
    }

    if ($user->hasAllRoles('student')) {
      $submissions = FormHelper::formHistory($req);
    }

    return view('content.dashboard.dashboard-main', compact(
      'activeForm',
      'sessions',
      'majors',
      'kpis',
      'prodiChart',
      'questionStackChart',
      'lectureReportData',
      'user',
      'reports',
      'submissions',
    ));
  }

  public function myDashboard(Request $req)
  {
    $user = Auth::user();
    $lectureReportData = HomeHelper::lectureReportData();

    return view('content.dashboard.dashboard-my', compact(
      'lectureReportData',
      'user',
    ));
  }
}
