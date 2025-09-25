<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
// use App\Http\Controllers\FormController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Global\Form\FormController;
use App\Http\Controllers\Global\Form\QuestionController;
use App\Http\Controllers\HomeController;

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
  Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');

  Route::prefix('form')->group(function () {
    Route::get('/', [FormController::class, 'index'])->name('form.index');
    Route::get('/active', [FormController::class, 'showActiveForm'])->name('form.active');
    Route::get('/create', [FormController::class, 'create'])->name('form.create');
    Route::post('/store', [FormController::class, 'store'])->name('form.store');
    Route::get('/{id}', [FormController::class, 'show'])->name('form.show');
    Route::get('/{id}/result', [FormController::class, 'showFormDetailSubmit'])->name('form.result');
    Route::get('/{id}/edit', [FormController::class, 'edit'])->name('form.edit');
    Route::put('/{id}', [FormController::class, 'update'])->name('form.update');
    Route::post('/{id}/restore', [FormController::class, 'restore'])->name('form.restore');
    Route::delete('/{id}', [FormController::class, 'delete'])->name('form.delete');
    Route::get('/{id}/fill', [FormController::class, 'fillForm'])->name('form.fill');
    Route::post('/{id}/submit', [FormController::class, 'submitForm'])->name('form.submit');

    Route::prefix('/{id}/questions')->group(function () {
      Route::get('/', [QuestionController::class, 'index'])->name('form.question.index');
      Route::get('/create', [QuestionController::class, 'create'])->name('form.question.create');
      Route::post('/store', [QuestionController::class, 'store'])->name('form.question.store');
      Route::get('/edit', [QuestionController::class, 'edit'])->name('form.question.edit');
      Route::put('/update', [QuestionController::class, 'update'])->name('form.question.update');
      // Route::delete('/{questionId}', [FormController::class, 'deleteQuestion'])->name('form.questions.delete');
    });
  });
});


// 🔹 Form routes (protected)
//Route::middleware('jwt.verify')->group(function () {
//     Route::get('/form/tambah', [FormController::class, 'tambahForm'])->name('form.tambah');
//     Route::post('/form/simpan', [FormController::class, 'simpanForm'])->name('form.simpan');

// Route::get('/form/{form}/edit', [FormController::class, 'editForm'])->name('form.edit');
// Route::put('/form/{form}/update', [FormController::class, 'updateForm'])->name('form.update');
// Route::delete('/form/{id}/hapus', [FormController::class, 'hapusForm'])->name('form.hapus');

// //});   // ✅ perbaikan disini

// // 🔹 Dashboard route
// // Route::get('/form', [FormController::class, 'showForm']);
// Route::get('/form/{id}/questions', [FormController::class, 'showQuestionList'])->name('form.questions');

// 🔹 Debugging purpose
Route::get('/check', [FormController::class, 'checkFile']);
