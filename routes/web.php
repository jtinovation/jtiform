<?php

use App\Http\Controllers\Api\SuperAppApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Global\Form\FormController;
use App\Http\Controllers\Global\Form\FormSubmissionController;
use App\Http\Controllers\Global\Form\FormSummaryController;
use App\Http\Controllers\Global\Form\QuestionController;
use App\Http\Controllers\Global\StudyProgramController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Lecture\EvaluationController;
use App\Http\Controllers\Global\EvaluationController as GlobalEvaluationController;

Route::get('/', function () {
  return redirect()->route('login');
});

// 🔹 Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::prefix('auth')->group(function () {
  Route::get('/login', [OAuthController::class, 'redirect'])->name('auth.login');
  Route::get('/callback', [OAuthController::class, 'callback'])->name('auth.callback');
  Route::post('/logout', [OAuthController::class, 'logout'])->name('auth.logout');
});

Route::middleware('auth')->group(function () {
  Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
  Route::get('/dashboard/my', [HomeController::class, 'myDashboard'])->name('dashboard.my');

  Route::prefix('form')->group(function () {
    Route::middleware('role:superadmin|admin|direktur|wadir|kajur|kaprodi')->group(function () {
      Route::get('/', [FormController::class, 'index'])->name('form.index');
      Route::get('/create', [FormController::class, 'create'])->name('form.create');
      Route::post('/store', [FormController::class, 'store'])->name('form.store');
    });

    Route::get('/active', [FormController::class, 'active'])->name('form.active');
    Route::get('/history', [FormController::class, 'history'])->name('form.history');

    Route::prefix('submission')->group(function () {
      Route::get('/proof/{id}', [FormSubmissionController::class, 'printProof'])->name('form.proof.print');
    });

    Route::prefix('/{id}')->group(function () {
      Route::get('/fill', [FormController::class, 'fill'])->name('form.fill');
      Route::post('/choose-lecture', [FormController::class, 'storeChosenLectures'])->name('form.choose-lecture.store');
      Route::get('/fill-lecture', [FormController::class, 'fillLecture'])->name('form.fill.lecture');
      Route::post('/submit', [FormController::class, 'submit'])->name('form.submit');
      Route::post('/submit-evaluation', [FormController::class, 'submitEvaluation'])->name('form.submit.evaluation');
      Route::get('/result', [FormController::class, 'showFormDetailSubmit'])->name('form.result');
      Route::get('/result-evaluation', [FormController::class, 'showEvaluationResult'])->name('form.result.evaluation');
      Route::get('/generate-report', [FormController::class, 'generateReport'])->name('form.generate.report');

      Route::prefix('/summary')->group(function () {
        Route::get('/', [FormController::class, 'showSummary'])->name('form.summary');
        Route::get('/export', [FormSummaryController::class, 'respondentsExport'])->name('form.summary.export');
        Route::get('/kpi', [FormSummaryController::class, 'kpi'])->name('form.summary.kpi');
        Route::get('/respondents', [FormSummaryController::class, 'respondents'])->name('form.summary.respondents');
        Route::get('/respondents/{submissionId}', [FormSummaryController::class, 'respondentDetail'])->name('form.summary.respondent.detail');
        Route::get('/question-stats', [FormSummaryController::class, 'questionStats'])->name('form.summary.question.stats');
        Route::get('/question/{questionId}/texts', [FormSummaryController::class, 'questionTexts'])->name('form.summary.question.texts');
      });

      Route::middleware('role:superadmin|admin|direktur|wadir|kajur|kaprodi')->group(function () {
        Route::get('/', [FormController::class, 'show'])->name('form.show');
        Route::get('/edit', [FormController::class, 'edit'])->name('form.edit');
        Route::put('/', [FormController::class, 'update'])->name('form.update');
        Route::post('/restore', [FormController::class, 'restore'])->name('form.restore');
        Route::delete('/', [FormController::class, 'delete'])->name('form.delete');
      });
    });

    Route::prefix('/{id}/questions')->middleware('role:superadmin|admin|direktur|wadir|kajur|kaprodi')->group(function () {
      Route::get('/', [QuestionController::class, 'index'])->name('form.question.index');
      Route::get('/create', [QuestionController::class, 'create'])->name('form.question.create');
      Route::post('/store', [QuestionController::class, 'store'])->name('form.question.store');
      Route::get('/edit', [QuestionController::class, 'edit'])->name('form.question.edit');
      Route::put('/update', [QuestionController::class, 'update'])->name('form.question.update');
      // Route::delete('/{questionId}', [FormController::class, 'deleteQuestion'])->name('form.questions.delete');
    });
  });

  Route::get('/evaluation', [GlobalEvaluationController::class, 'index'])->name('evaluation.index');

  Route::prefix('lecture')->middleware('role:superadmin|admin|lecturer')->group(function () {
    Route::get('/evaluation', [EvaluationController::class, 'index'])->name('lecture.evaluation.index');
    Route::get('/evaluation/my', [EvaluationController::class, 'my'])->name('lecture.evaluation.my.index');
    Route::get('/evaluation/{userId}/show', [EvaluationController::class, 'show'])->name('lecture.evaluation.show');
    Route::get('/evaluation/{id}/report-pdf', [EvaluationController::class, 'generateReportPdf'])->name('lecture.evaluation.report.pdf');
    Route::get('/evaluation/{formId}/report-pdf-all', [EvaluationController::class, 'generateReportPdfAll'])->name('lecture.evaluation.report.pdf.all');
  });

  Route::prefix('study-program')->group(function () {
    Route::get('/evaluation', [StudyProgramController::class, 'reportProgram'])->name('study.program.evaluation.index');
    Route::get('/evaluation/data', [StudyProgramController::class, 'getDataReportProgram'])->name('study.program.evaluation.data');
    Route::post('/evaluation/generate', [StudyProgramController::class, 'generateData'])->name('study.program.evaluation.generate');
    Route::post('/evaluation/regenerate', [StudyProgramController::class, 'regenerateData'])->name('study.program.evaluation.regenerate');
  });

  Route::prefix('api')->group(function () {
    Route::get('/major/option', [SuperAppApiController::class, 'majorOption'])->name('api.major.option');
    Route::get('/study-program/option', [SuperAppApiController::class, 'studyProgramOption'])->name('api.study_program.option');
    Route::get('/student/option', [SuperAppApiController::class, 'studentOption'])->name('api.student.option');
    Route::get('/staff/option', [SuperAppApiController::class, 'staffOption'])->name('api.staff.option');
    Route::get('/session/option', [SuperAppApiController::class, 'sessionOption'])->name('api.session.option');
  });
});
