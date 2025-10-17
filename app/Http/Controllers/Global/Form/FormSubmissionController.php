<?php

namespace App\Http\Controllers\Global\Form;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use tbQuar\Facades\Quar;

class FormSubmissionController extends Controller
{
  public function printProof($id)
  {
    $user = Auth::user();
    $submission = Submission::where('m_user_id', $user->id)
      ->with('form')
      ->firstOrFail();

    $me = ApiHelper::getMe($user->token);
    $user->details = $me;

    $qrCode = Quar::size(100)
      // ->merge(public_path('assets/img/logo/polije.png'), .3, true)
      ->generate(route('form.proof.print', ['id' => $submission->id]));

    return view('content.form.student.proof', compact('submission', 'user', 'qrCode'));
  }
}
