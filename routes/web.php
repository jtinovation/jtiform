<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return redirect()->route('login');
});

// 🔹 Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 🔹 Protected route → hanya bisa diakses setelah login
Route::get('/dashboard', [HomeController::class, 'index']);
// Route::get('/dashboard', [HomeController::class, 'index'])->middleware('jwt.verify');


// 🔹 Dashboard route
Route::get('/form', [FormController::class, 'showActiveForm']);
Route::get('/form/form-master', [FormController::class, 'showForm']);

// 🔹 Debugging purpose
Route::get('/table', [FormController::class, 'checkTable']);
