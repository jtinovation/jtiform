{{-- resources/views/landing/index.blade.php --}}
@extends('layouts/commonMaster')

@section('title', 'JTIForm — Landing')

@push('styles')
    @vite('resources/assets/vendor/scss/landing.scss')
@endpush

@section('layoutContent')
    {{-- HERO --}}
    <section class="hero position-relative overflow-hidden">
        <div class="hero__bg" role="img" aria-label="Gedung POLIJE"></div>
        <span class="hero__overlay"></span>

        <div class="container position-relative">
            <div class="row align-items-center py-5">
                <div class="col-12 col-lg-7 text-white">
                    <h1 class="display-5 fw-bold mb-3 text-white">
                        JTIForm — Evaluasi Dosen & Form Akademik Terintegrasi
                    </h1>
                    <p class="lead mb-4">
                        Kumpulkan penilaian dosen tiap semester dan kelola form umum seperti Google Forms—
                        semuanya terintegrasi dengan SSO kampus, laporan real-time, dan keamanan terbaik.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('auth.login') }}" class="btn btn-primary btn-lg px-4"
                            aria-label="Login melalui SSO kampus">
                            <i class="ti ti-login me-2"></i> Login SSO
                        </a>
                        {{-- <a href="#contoh-form" class="btn btn-outline-light btn-lg px-4">
                            <i class="ti ti-eye me-2"></i> Lihat Contoh Form
                        </a> --}}
                    </div>
                </div>

                <div class="col-12 col-lg-5 mt-5 mt-lg-0">
                    {{-- Mockup cards ala Materio --}}
                    <div class="card glass-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <h6 class="mb-1 text-white">Evaluasi Dosen Semester Ganjil 2025/2026</h6>
                                    <small class="text-muted">Teknik Informatika · 32 Kelas</small>
                                </div>
                                <span class="badge bg-label-success">Aktif</span>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 8px" aria-label="Progress pengisian">
                                    <div class="progress-bar" role="progressbar" style="width: 62%"></div>
                                </div>
                                <small class="text-muted d-block mt-1">62% terkumpul</small>
                            </div>
                        </div>
                    </div>

                    <div class="card glass-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0 text-white">Survei Kepuasan Layanan</h6>
                                <span class="badge bg-label-primary">Publik</span>
                            </div>
                            {{-- Mini bar chart dummy --}}
                            <div class="d-flex gap-2 align-items-end" style="height: 72px">
                                <div class="chart-bar" style="height: 45px"></div>
                                <div class="chart-bar" style="height: 65px"></div>
                                <div class="chart-bar" style="height: 32px"></div>
                                <div class="chart-bar" style="height: 58px"></div>
                                <div class="chart-bar" style="height: 50px"></div>
                            </div>
                            <small class="text-muted">Rata-rata skor minggu ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Logo pojok optional --}}
        <img src="{{ Vite::asset('resources/assets/images/polije.png') }}" alt="Logo JTIForm"
            class="hero__logo d-none d-md-block" />
    </section>

    {{-- KEUNGGULAN --}}
    <section class="py-5" id="fitur">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="mb-1">Keunggulan</h2>
                <p class="text-muted mb-0">Performa kencang, integrasi rapi.</p>
            </div>

            <div class="row g-4">
                @php
                    $features = [
                        [
                            'icon' => 'ri-clipboard-line',
                            'title' => 'Evaluasi Dosen Tiap Semester',
                            'desc' => 'Penilaian per mata kuliah & dosen, indikator standar kampus.',
                        ],
                        [
                            'icon' => 'ri-file-list-3-line',
                            'title' => 'Form Umum Mirip Google Form',
                            'desc' => 'Checkbox, pilihan ganda, skala, esai—fleksibel & mudah.',
                        ],
                        [
                            'icon' => 'ri-lock-line',
                            'title' => 'SSO Terintegrasi',
                            'desc' => 'Login tunggal untuk mahasiswa & dosen. Aman & cepat.',
                        ],
                        [
                            'icon' => 'ri-bar-chart-line',
                            'title' => 'Laporan & Visualisasi',
                            'desc' => 'Grafik distribusi nilai, rata-rata per pertanyaan, export PDF.',
                        ],
                        [
                            'icon' => 'ri-shield-check-line',
                            'title' => 'Kontrol Hak Akses',
                            'desc' => 'Role-based: admin, jurusan, prodi, dosen.',
                        ],
                        [
                            'icon' => 'ri-rocket-line',
                            'title' => 'Performa & Keamanan',
                            'desc' => 'Anti-spam dasar, batas submit, header security best-practice.',
                        ],
                    ];
                @endphp

                @foreach ($features as $f)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 shadow-sm hover-lift">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="{{ $f['icon'] }} text-primary me-3 fs-3"></i>
                                    <h5 class="mb-0">{{ $f['title'] }}</h5>
                                </div>
                                <p class="mb-0 text-muted">{{ $f['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CARA KERJA --}}
    <section class="py-5 bg-body-tertiary" id="cara-kerja">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="mb-1">Cara Kerja</h2>
                <p class="text-muted mb-0">Empat langkah sederhana.</p>
            </div>
            <div class="row g-4">
                @php
                    $steps = [
                        ['icon' => 'ri-login-box-line', 'title' => 'Login SSO', 'desc' => 'Masuk dengan akun kampus.'],
                        [
                            'icon' => 'ri-file-text-line',
                            'title' => 'Pilih Form',
                            'desc' => 'Evaluasi Dosen atau Form Umum.',
                        ],
                        [
                            'icon' => 'ri-send-plane-line',
                            'title' => 'Isi & Kumpulkan',
                            'desc' => 'Kirim jawaban dengan mudah dan cepat.',
                        ],
                        [
                            'icon' => 'ri-file-download-line',
                            'title' => 'Analisis & Unduh',
                            'desc' => 'Dashboard, export PDF/Excel.',
                        ],
                    ];
                @endphp
                @foreach ($steps as $idx => $s)
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 text-center shadow-sm">
                            <div class="card-body">
                                <div class="step-circle mx-auto mb-3">{{ $idx + 1 }}</div>
                                <i class="{{ $s['icon'] }} fs-2 text-primary mb-2"></i>
                                <h6 class="mb-1">{{ $s['title'] }}</h6>
                                <p class="text-muted mb-0">{{ $s['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- <section class="py-5" id="contoh-form">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="mb-1">Cuplikan UI</h2>
                <p class="text-muted mb-0">Sekilas tampilan builder & laporan.</p>
            </div>

            <div id="showcaseCarousel" class="carousel slide" data-bs-ride="false" aria-label="Cuplikan UI JTIForm">
                <div class="carousel-inner rounded-3 shadow-sm">
                    <div class="carousel-item active">
                        <img src="{{ Vite::asset('resources/assets/images/mock-builder.png') }}" class="d-block w-100"
                            alt="Builder Pertanyaan">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ Vite::asset('resources/assets/images/mock-table.png') }}" class="d-block w-100"
                            alt="Tabel Responden">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ Vite::asset('resources/assets/images/mock-chart.png') }}" class="d-block w-100"
                            alt="Chart Rata-rata">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#showcaseCarousel"
                    data-bs-slide="prev" aria-label="Sebelumnya">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#showcaseCarousel"
                    data-bs-slide="next" aria-label="Berikutnya">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </section>

    <section class="py-5 bg-body-tertiary">
        <div class="container">
            <div class="row g-4 text-center">
                @php
                    $stats = [
                        ['val' => '+10K', 'label' => 'Submissions'],
                        ['val' => '100+', 'label' => 'Dosen'],
                        ['val' => '30+', 'label' => 'Program Studi'],
                        ['val' => '99.9%', 'label' => 'Uptime*'],
                    ];
                @endphp
                @foreach ($stats as $st)
                    <div class="col-6 col-md-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="fs-3 fw-bold text-primary">{{ $st['val'] }}</div>
                                <div class="text-muted">{{ $st['label'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <small class="text-muted mt-2">*estimasi target operasional</small>
            </div>
        </div>
    </section> --}}

    {{-- FAQ --}}
    <section class="py-5" id="faq">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="mb-1">FAQ</h2>
                <p class="text-muted mb-0">Pertanyaan yang sering diajukan</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="accordion" id="faqAcc">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="q1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#a1" aria-expanded="true" aria-controls="a1">
                                    Apakah harus pakai akun kampus?
                                </button>
                            </h2>
                            <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faqAcc">
                                <div class="accordion-body">
                                    Ya, login melalui SSO kampus untuk keamanan & kemudahan.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="q2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#a2" aria-expanded="false" aria-controls="a2">
                                    Bisakah ekspor ke Excel/PDF?
                                </button>
                            </h2>
                            <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                                <div class="accordion-body">
                                    Bisa, laporan dosen serta rekap responden mendukung ekspor Excel/PDF.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="q3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#a3" aria-expanded="false" aria-controls="a3">
                                    Apakah mendukung skala penilaian?
                                </button>
                            </h2>
                            <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                                <div class="accordion-body">
                                    Ya, tersedia skala likert & poin, plus opsi teks/checkbox/pilihan ganda.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA BOTTOM --}}
    <section class="py-5 bg-gradient-primary text-white text-center">
        <div class="container">
            <h2 class="mb-3 text-white">Siap Memulai Evaluasi & Survei Lebih Cepat?</h2>
            <a href="{{ route('auth.login') }}" class="btn btn-light btn-lg">
                <i class="ti ti-login me-2"></i> Login SSO
            </a>
        </div>
    </section>

    {{-- Footer minimal (bisa pakai footer Materio default juga) --}}
    <footer class="py-4">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ Vite::asset('resources/assets/images/polije.png') }}" alt="JTIForm" height="24">
                <span class="text-muted">JTIForm · Politeknik Negeri Jember (POLIJE)</span>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="#fitur" class="text-muted me-3">Fitur</a>
                <a href="#faq" class="text-muted me-3">FAQ</a>
                <a href="#" class="text-muted">Kebijakan Privasi</a>
            </div>
        </div>
    </footer>
@endsection

@push('page-script')
    @vite('resources/assets/js/landing.js')
@endpush
