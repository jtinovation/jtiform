<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function showActiveForm()
    {
      $forms = Form::where('is_active', 1)->get();

      return view('content.form.form-active', compact('forms'));
    }

    public function showForm()
    {
      $forms = Form::get();

      return view('content.form.form-master', compact('forms'));
    }


    public function showQuestionList($id)
    {
      $questions = Question::where('m_form_id', $id)->orderBy('sequence', 'asc')->get();

      return view('content.form.questions', compact('questions'));
    }

    public function checkTable()
    {
      return view('content.user-interface.ui-buttons');
    }
}
