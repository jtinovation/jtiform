@extends('layouts/contentNavbarLayout')

@section('title', 'Pilih Dosen Pengampu')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.active') }}">Form</a></li>
            <li class="breadcrumb-item active">Pilih Dosen</li>
        </ol>
    </nav>

    {{-- Alert success/error --}}
    {{-- @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @error('selections')
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @enderror --}}

    {{-- Header Info Form --}}
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="mb-1">{{ $form->title ?? 'Form Evaluasi Dosen' }}</h5>
                <div class="text-muted small">
                    Waktu Pengisian: {{ \Illuminate\Support\Carbon::parse($form->start_at ?? now())->format('d M Y') }}
                    — {{ \Illuminate\Support\Carbon::parse($form->end_at ?? now())->format('d M Y') }}
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-label-primary text-uppercase">Prodi: {{ $studyProgramName }}</span>
            </div>
        </div>
    </div>

    <form id="choose-lecture-form" action="{{ route('form.choose-lecture.store', $form->id) }}" method="POST" novalidate>
        @csrf

        {{-- Grid daftar matkul --}}
        <div class="row g-3">
            @foreach ($lectures as $idx => $row)
                @php
                    $subject = $row['subject'] ?? [];
                    $subjectId = $subject['id'] ?? 'subject-' . $idx;
                    $subjectName = $subject['name'] ?? 'Mata Kuliah';
                    $subjectCode = $subject['code'] ?? '—';
                    $listLectures = $row['lectures'] ?? [];

                    // Prefill dari Redis/old input
                    $selected = old("selections.$subjectId", $prefill[$subjectId] ?? []);
                    if (!is_array($selected)) {
                        $selected = [];
                    }
                @endphp

                <div class="col-12">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0">{{ $subjectName }}</h6>
                                <small class="text-muted">Kode: {{ $subjectCode }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-info">
                                    <span class="me-1">Dipilih:</span>
                                    <span class="fw-semibold" data-selected-count="{{ $subjectId }}">0</span>
                                </span>
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-action="clear-subject"
                                    data-subject="{{ $subjectId }}">
                                    Bersihkan
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            @if (count($listLectures) === 0)
                                <div class="alert alert-warning mb-0">
                                    Belum ada dosen terdata untuk mata kuliah ini.
                                </div>
                            @else
                                <div class="row g-2">
                                    @foreach ($listLectures as $i => $lec)
                                        @php
                                            $lecId = $lec['id'] ?? 'lec-' . $i;
                                            $lecUser = $lec['user'] ?? [];
                                            $lecName = $lecUser['name'] ?? 'Nama Dosen';
                                            $isChecked = in_array($lecId, $selected, true);
                                        @endphp
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-check custom-option custom-option-basic">
                                                <label class="custom-option-content w-100">
                                                    <input type="checkbox" class="form-check-input me-2 subject-checkbox"
                                                        name="selections[{{ $subjectId }}][]"
                                                        value="{{ $lecId }}" data-subject="{{ $subjectId }}"
                                                        @checked($isChecked)>
                                                    <span class="custom-option-header">
                                                        <span class="h6 mb-0">{{ $lecName }}</span>
                                                    </span>
                                                    {{-- <span class="custom-option-body">
                                                        <small class="text-muted d-block">ID: {{ $lecId }}</small>
                                                    </span> --}}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-sm btn-primary" type="button" data-action="select-all"
                                        data-subject="{{ $subjectId }}">
                                        Pilih Semua
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-action="select-none"
                                        data-subject="{{ $subjectId }}">
                                        Tidak Pilih
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer text-muted small">
                            Wajib memilih minimal <strong>1 dosen</strong> untuk mata kuliah ini.
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 mt-4">
            <a href="{{ route('form.active') }}" class="btn btn-text-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('choose-lecture-form');

            // Hitung selected per subject
            function recalc(subjectId) {
                const boxes = document.querySelectorAll(`input.subject-checkbox[data-subject="${subjectId}"]`);
                let count = 0;
                boxes.forEach(b => {
                    if (b.checked) count++;
                });
                const counter = document.querySelector(`[data-selected-count="${subjectId}"]`);
                if (counter) counter.textContent = count;
                return count;
            }

            // Inisialisasi counter
            const subjects = new Set(Array.from(document.querySelectorAll('input.subject-checkbox')).map(b => b
                .dataset.subject));
            subjects.forEach(sid => recalc(sid));

            // Listener change checkbox
            document.querySelectorAll('input.subject-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    recalc(this.dataset.subject);
                });
            });

            // Select all / none / clear per subject
            document.querySelectorAll('[data-action="select-all"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const subjectId = this.dataset.subject;
                    document.querySelectorAll(`input.subject-checkbox[data-subject="${subjectId}"]`)
                        .forEach(cb => cb.checked = true);
                    recalc(subjectId);
                });
            });
            document.querySelectorAll('[data-action="select-none"],[data-action="clear-subject"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const subjectId = this.dataset.subject;
                    document.querySelectorAll(`input.subject-checkbox[data-subject="${subjectId}"]`)
                        .forEach(cb => cb.checked = false);
                    recalc(subjectId);
                });
            });

            // Validasi: setiap subject minimal 1 dosen
            form.addEventListener('submit', function(e) {
                let valid = true;
                subjects.forEach(sid => {
                    const count = recalc(sid);
                    const card = document.querySelector(`[data-selected-count="${sid}"]`)?.closest(
                        '.card');
                    if (count < 1) {
                        valid = false;
                        if (card) {
                            card.classList.add('border', 'border-danger');
                            // Scroll ke card yang invalid (pertama)
                            card.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    } else {
                        if (card) {
                            card.classList.remove('border', 'border-danger');
                        }
                    }
                });
                if (!valid) {
                    e.preventDefault();
                    // Tampilkan toast/alert sederhana
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger mt-3';
                    alert.role = 'alert';
                    alert.textContent = 'Setiap mata kuliah wajib memilih minimal 1 dosen.';
                    form.prepend(alert);
                    setTimeout(() => alert.remove(), 4000);
                }
            });
        });
    </script>
@endsection
