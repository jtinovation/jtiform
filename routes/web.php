<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
  return redirect()->route('login');
});

// 🔹 Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');

// 🔹 Protected route → hanya bisa diakses setelah login
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');

Route::prefix('auth')->group(function () {
  Route::get('/login', [OAuthController::class, 'redirect'])->name('auth.login');
  Route::get('/callback', [OAuthController::class, 'callback'])->name('auth.callback');
  Route::post('/logout', [OAuthController::class, 'logout'])->name('auth.logout');
});

// 🔹 Form routes
Route::get('/form-active', [FormController::class, 'showActiveForm'])->name('form.active');
Route::get('/form/create', [FormController::class, 'createForm'])->name('form.create');
Route::post('/form/store', [FormController::class, 'storeForm'])->name('form.store');

Route::get('/form/{form}/edit', [FormController::class, 'editForm'])->name('form.edit');
Route::put('/form/{form}/update', [FormController::class, 'updateForm'])->name('form.update');
Route::delete('/form/{id}/delete', [FormController::class, 'deleteForm'])->name('form.delete');

// 🔹 Dashboard route
Route::get('/form', [FormController::class, 'showForm']);


// Daftar pertanyaan
Route::get('/forms/{form}/questions', [QuestionController::class, 'indexQuestion'])
    ->name('forms.questions.index');

// Tambah pertanyaan
Route::get('/forms/{form}/questions/create', [QuestionController::class, 'createQuestion'])
    ->name('question.create');
Route::post('/forms/{form}/questions', [QuestionController::class, 'storeQuestion'])
    ->name('question.store');

// Edit pertanyaan
Route::get('/forms/{form}/questions/{question}/edit', [QuestionController::class, 'editQuestion'])
    ->name('question.edit');

// Update pertanyaan
Route::put('/forms/{form}/questions/{question}/update', [QuestionController::class, 'updateQuestion'])
    ->name('question.update');

// Hapus pertanyaan
Route::delete('/forms/{form}/questions/{question}/delete', [QuestionController::class, 'deleteQuestion'])
    ->name('question.delete');

// 🔹 Debugging purpose
Route::get('/check', [FormController::class, 'checkFile']);
