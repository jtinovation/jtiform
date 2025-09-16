<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FormController;

Route::get('/', function () {
    return redirect()->route('login');
});

// 🔹 Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 🔹 Protected route → hanya bisa diakses setelah login
Route::get('/dashboard', [HomeController::class, 'index'])->middleware('jwt.verify');

// 🔹 Form routes (protected)
Route::middleware('jwt.verify')->group(function () {
    Route::get('/form', [FormController::class, 'listForm'])->name('form.list');
    Route::get('/form/tambah', [FormController::class, 'tambahForm'])->name('form.tambah');
    Route::post('/form/simpan', [FormController::class, 'simpanForm'])->name('form.simpan');

    Route::get('/form/{form}/edit', [FormController::class, 'editForm'])->name('form.edit');
    Route::put('/form/{form}/update', [FormController::class, 'updateForm'])->name('form.update');
    Route::delete('/form/{form}/hapus', [FormController::class, 'hapusForm'])->name('form.hapus');
});
