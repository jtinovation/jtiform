<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Helpers\FormHelper;
use App\Models\Form;
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

    $prefiltered = $this->formHelper->getFormVisibleToUser($user, null, false);

    return view('content.dashboard.dashboard-main', [
      'user'       => $user,
      'activeForm' => $prefiltered,
    ]);
  }
}
