<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Middleware supaya halaman dashboard hanya bisa diakses kalau user punya JWT
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    // Fungsi utama untuk menampilkan dashboard
    public function index()
    {
        return view('content.dashboard.dashboard-main');
    }
}
