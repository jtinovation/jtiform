<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
  return redirect()->route('login');
});

// 🔹 Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');

// 🔹 Protected route → hanya bisa diakses setelah login
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');
// Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::prefix('auth')->group(function () {
  Route::get('/login', [OAuthController::class, 'redirect'])->name('auth.login');
  Route::get('/callback', [OAuthController::class, 'callback'])->name('auth.callback');
  Route::post('/logout', [OAuthController::class, 'logout'])->name('auth.logout');
});

// 🔹 Form routes (protected)
//Route::middleware('jwt.verify')->group(function () {
    Route::get('/form-active', [FormController::class, 'showActiveForm'])->name('form.active');
    Route::get('/form/tambah', [FormController::class, 'tambahForm'])->name('form.tambah');
    Route::post('/form/simpan', [FormController::class, 'simpanForm'])->name('form.simpan');

Route::get('/form/{form}/edit', [FormController::class, 'editForm'])->name('form.edit');
Route::put('/form/{form}/update', [FormController::class, 'updateForm'])->name('form.update');
Route::delete('/form/{id}/hapus', [FormController::class, 'hapusForm'])->name('form.hapus');

//});   // ✅ perbaikan disini

// 🔹 Dashboard route
Route::get('/form', [FormController::class, 'showForm']);
Route::get('/form/{id}/questions', [FormController::class, 'showQuestionList'])->name('form.questions');

// 🔹 Debugging purpose
Route::get('/table', [FormController::class, 'checkTable']);
