@extends('layouts/contentNavbarLayout')

@section('title', 'Mengerjakan Form')

@section('page-style')
    <style>
        .drag-handle {
            opacity: .35;
            cursor: default;
            font-size: 18px;
            line-height: 1;
            padding: 0 .25rem
        }

        .question-item .card-header {
            background: #fafafa
        }

        .opt-seq {
            min-width: 2.25rem;
            text-align: center
        }

        .badge-soft {
            background: #f3f6ff;
            color: #2f3b52;
            border: 1px solid #e5ebff;
        }

        .badge-type {
            text-transform: uppercase;
            letter-spacing: .3px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.active') }}">Form</a></li>
            <li class="breadcrumb-item active">Mengerjakan Form</li>
        </ol>
    </nav>

    <form id="answerForm" action="{{ route('form.submit', $form->id) }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Header Info Form --}}
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-start gap-3">
                            <div class="flex-grow-1">
                                <h4 class="mb-1">{{ $form->title ?? 'Tanpa Judul' }}</h4>
                                @if (!empty($form->description))
                                    <p class="mb-0 text-muted">{{ $form->description }}</p>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('form.active', $form->id) }}"
                                    class="btn btn-outline-secondary">Kembali</a>
                            </div>
                        </div>
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <span class="badge bg-label-primary">Total Pertanyaan: {{ $questions->count() }}</span>
                            {{-- contoh meta lain kalau ada --}}
                            {{-- <span class="badge bg-label-info">Aktif s/d: {{ optional($form->end_at)->format('d M Y') }}</span> --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Pertanyaan (Read-only) --}}
            <div class="col-12">
                <div id="question-list" class="d-grid gap-3">

                    @forelse($questions as $i => $q)
                        @php
                            $isChoice = in_array($q->type, ['checkbox', 'option']);
                            $opts = ($q->options ?? collect())->sortBy('sequence')->values();
                        @endphp

                        <div class="question-item card shadow-sm">
                            <div class="card-header d-flex align-items-center gap-2 py-2">
                                <span class="drag-handle">☰</span>
                                <strong class="me-auto">Pertanyaan {{ $i + 1 }}</strong>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-soft badge-type">
                                        {{ $q->type === 'text' ? 'Text' : ($q->type === 'checkbox' ? 'Checkbox' : 'Option') }}
                                    </span>
                                    @if ($q->is_required)
                                        <span class="badge bg-label-danger">Wajib</span>
                                    @else
                                        <span class="badge bg-label-secondary">Opsional</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body pt-3">
                                {{-- Teks pertanyaan --}}
                                <div class="mb-3">
                                    <div class="fw-medium">{{ $q->question }}</div>
                                </div>

                                {{-- Tampilan jawaban sesuai tipe --}}
                                @if (!$isChoice)
                                    {{-- Tipe Text --}}
                                    <div class="form-floating form-floating-outline" style="max-width:560px">
                                        <input type="text" class="form-control" name="answer[{{ $q->id }}]"
                                            placeholder="Jawaban singkat" {{ $q->is_required ? 'required' : '' }}>
                                        <label>Jawaban singkat</label>
                                    </div>
                                @else
                                    {{-- Tipe Checkbox/Option --}}
                                    <div class="mb-2">
                                        @forelse($opts as $j => $opt)
                                            @if ($q->type === 'checkbox')
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="answer[{{ $q->id }}][]" value="{{ $opt->id }}"
                                                        id="option_{{ $q->id }}_{{ $opt->id }}">
                                                    <label class="form-check-label"
                                                        for="option_{{ $q->id }}_{{ $opt->id }}">
                                                        {{ $opt->answer }}
                                                    </label>
                                                </div>
                                            @else
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio"
                                                        name="answer[{{ $q->id }}]" value="{{ $opt->id }}"
                                                        id="option_{{ $q->id }}_{{ $opt->id }}"
                                                        {{ $q->is_required ? 'required' : '' }}>
                                                    <label class="form-check-label"
                                                        for="option_{{ $q->id }}_{{ $opt->id }}">
                                                        {{ $opt->answer }}
                                                    </label>
                                                </div>
                                            @endif
                                        @empty
                                            <div class="text-muted fst-italic">Belum ada opsi.</div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning mb-0">Belum ada pertanyaan pada form ini.</div>
                    @endforelse

                </div>
            </div>

            <div class="col-12">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="notificationToast" class="bs-toast toast fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i id="toastIcon" class="ri-information-line me-2"></i>
                <div class="me-auto fw-medium" id="toastTitle">Notification</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                <!-- Message here -->
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Submit -->
    <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSubmitModalLabel">
                        <i class="ri-question-line me-2 text-warning"></i>
                        Konfirmasi Pengisian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="ri-file-check-line text-primary mb-3" style="font-size: 3rem;"></i>
                        <h6 class="mb-2">Apakah Anda yakin ingin mengirim jawaban?</h6>
                        <p class="text-muted small mb-0">
                            Setelah dikirim, jawaban tidak dapat diubah lagi.
                            Pastikan semua jawaban sudah benar.
                        </p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="submitSpinner"></span>
                        <i class="ri-send-plane-line me-1"></i>
                        Ya, Kirim Jawaban
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showNotification(message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');

            toastMessage.textContent = message;

            toast.className = 'bs-toast toast fade';

            if (type === 'success') {
                toastIcon.className = 'ri-checkbox-circle-fill text-success me-2';
                toastTitle.textContent = 'Success';
            } else if (type === 'warning') {
                toastIcon.className = 'ri-error-warning-fill text-warning me-2';
                toastTitle.textContent = 'Warning';
            } else if (type === 'error' || type === 'danger') {
                toastIcon.className = 'ri-close-circle-fill text-danger me-2';
                toastTitle.textContent = 'Error';
            } else {
                toastIcon.className = 'ri-information-fill text-info me-2';
                toastTitle.textContent = 'Info';
            }

            toast.classList.add('show');
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="text"]').forEach(function(input) {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    const name = this.name;
                    document.querySelectorAll(`input[name="${name}"]`).forEach(function(r) {
                        r.classList.remove('is-invalid');
                    });
                });
            });

            document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const name = this.name;
                    document.querySelectorAll(`input[name="${name}"]`).forEach(function(c) {
                        c.classList.remove('is-invalid');
                    });
                });
            });

            document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
                const btn = this;
                const spinner = document.getElementById('submitSpinner');
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmSubmitModal'));

                btn.disabled = true;
                spinner.classList.remove('d-none');
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Mengumpulkan...';

                setTimeout(() => {
                    document.getElementById('answerForm').submit();
                }, 1000);
            });
        });

        function submitForm() {
            let hasError = false;
            let firstError = null;

            document.querySelectorAll('input[type="text"][required]').forEach(function(input) {
                if (input.value.trim() === '') {
                    input.classList.add('is-invalid');
                    hasError = true;
                    if (!firstError) firstError = input;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            document.querySelectorAll('input[type="radio"][required]').forEach(function(radio) {
                const name = radio.name;
                const isChecked = document.querySelector(`input[name="${name}"]:checked`);

                if (!isChecked) {
                    document.querySelectorAll(`input[name="${name}"]`).forEach(function(r) {
                        r.classList.add('is-invalid');
                    });
                    hasError = true;
                    if (!firstError) firstError = radio;
                } else {
                    document.querySelectorAll(`input[name="${name}"]`).forEach(function(r) {
                        r.classList.remove('is-invalid');
                    });
                }
            });

            if (hasError) {
                showNotification('Mohon lengkapi semua pertanyaan yang wajib diisi!', 'warning');

                if (firstError) {
                    const questionCard = firstError.closest('.question-item');
                    if (questionCard) {
                        questionCard.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        setTimeout(() => {
                            firstError.focus();
                        }, 500);
                    }
                }
            } else {
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
                confirmModal.show();
            }
        }
    </script>
@endsection
