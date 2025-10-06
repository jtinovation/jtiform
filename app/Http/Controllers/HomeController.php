<?php

namespace App\Http\Controllers;

use App\Enums\FormTypeEnum;
use App\Helpers\ApiHelper;
use App\Helpers\FormHelper;
use App\Helpers\SubjectLectureApiHelper;
use App\Models\Form;
use App\Models\Report;
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


  public function index()
  {
    $user = $this->apiHelper->getMe(Auth::user()->token);
    $activeForm = $this->formHelper->getFormVisibleToUser($user, null, false);

    if (Auth::user()->hasRole('lecturer')) {
      $reports = Report::where('m_user_id', Auth::user()->id)
        ->with('form')
        ->where('overall_average_score', '>', 0)
        ->orderBy('created_at', 'desc')
        ->get();

      $reportChartData = [
        'chartLabels' => $reports->pluck('form.code'),
        'chartData' => $reports->pluck('overall_average_score'),
        'chartPredicates' => $reports->pluck('predicate'),
      ];
    } else {
      $reports = [];
      $reportChartData = [
        'chartLabels' => [],
        'chartData' => [],
        'chartPredicates' => [],
      ];
    }


    return view('content.dashboard.dashboard-main', compact('activeForm', 'reports', 'reportChartData'));
  }
}
