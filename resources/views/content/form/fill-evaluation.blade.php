@extends('layouts/contentNavbarLayout')

@section('title', 'Mengerjakan Form')

@section('page-style')
    <style>
        .drag-handle {
            opacity: .35;
            cursor: default;
            font-size: 18px;
            line-height: 1;
            padding: 0.25rem
        }

        .question-item.card-header {
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

        .card-lecturer.invalid {
            border: 1px solid #dc3545 !important;
        }

        #subjectTabs {
            --tab-indicator-offset: 8px;
            /* atur jarak indikator dari dasar UL */
            position: relative;
            overflow-x: auto;
            overflow-y: visible;
            /* penting */
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            padding-bottom: calc(var(--tab-indicator-offset) + 2px);
            /* beri ruang di bawah UL */
        }

        #subjectTabs .nav-link {
            white-space: nowrap;
        }

        #subjectTabs::-webkit-scrollbar {
            height: 6px;
        }

        /* Sembunyikan indikator bawaan tema (kalau ada) agar tidak dobel */
        #subjectTabs .tab-slider,
        #subjectTabs .tab-indicator,
        #subjectTabs .slider {
            display: none !important;
        }

        /* Indicator custom kita */
        #subjectTabs .tab-active-indicator {
            position: absolute;
            bottom: 0;
            height: 2px;
            background: var(--bs-primary);
            border-radius: 2px;
            transition: left .25s ease, width .25s ease;
            /* memastikan di atas border-bottom nav-tabs */
            z-index: 1;
        }
    </style>
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.active') }}">Form</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.fill', $form->id) }}">Pilih Dosen</a></li>
            <li class="breadcrumb-item active">Mengerjakan Form</li>
        </ol>
    </nav>

    <form id="answerForm" action="{{ route('form.submit.evaluation', $form->id) }}" method="POST">
        @csrf

        {{-- Header --}}
        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap align-items-start gap-3">
                <div class="flex-grow-1">
                    <h4 class="mb-1">{{ $form->title ?? 'Tanpa Judul' }}</h4>
                    @if (!empty($form->description))
                        <p class="mb-0 text-muted">{{ $form->description }}</p>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('form.active', $form->id) }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
            <div class="card-body pt-0">
                <span class="badge bg-label-primary">Total Pertanyaan: {{ $questions->count() }}</span>
            </div>
        </div>

        {{-- Nav Tabs per Mata Kuliah --}}
        @php
            // Sanitizer untuk id html
            $sid = function ($s) {
                return preg_replace('/[^A-Za-z0-9_-]/', '_', $s);
            };
        @endphp

        <ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" id="subjectTabs" role="tablist"
            style="white-space: nowrap;">
            @foreach ($selectedLecturesWithDetail as $sIndex => $subjectData)
                @php
                    $subject = $subjectData['subject'] ?? [];
                    $subjectId = $subject['id'] ?? 'subject_' . $sIndex;
                    $tabId = 'sub_' . $sid($subjectId);
                    $subjectName = $subject['name'] ?? 'Mata Kuliah';
                    $subjectCode = $subject['code'] ?? '—';
                    $lectCount = count($subjectData['lectures'] ?? []);
                @endphp
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tabId }}-tab"
                        data-bs-toggle="tab" data-bs-target="#{{ $tabId }}" type="button" role="tab"
                        aria-controls="{{ $tabId }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        {{ $subjectName }} <span class="text-muted">({{ $subjectCode }})</span>
                        <span class="badge bg-label-info ms-1">{{ $lectCount }} dosen</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="subjectTabsContent">
            @foreach ($selectedLecturesWithDetail as $sIndex => $subjectData)
                @php
                    $subject = $subjectData['subject'] ?? [];
                    $subjectId = $subject['id'] ?? 'subject_' . $sIndex;
                    $tabId = 'sub_' . $sid($subjectId);
                    $lectures = $subjectData['lectures'] ?? [];
                @endphp

                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}"
                    role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
                    {{-- Hidden target list untuk server (opsional, membantu parsing) --}}
                    @foreach ($lectures as $lec)
                        @php
                            $slid = $lec['subject_lecture_id'] ?? $lec['id'] . '_' . $subjectId;
                        @endphp
                        <input type="hidden" name="targets[{{ $subjectId }}][]" value="{{ $slid }}">
                    @endforeach

                    <div class="row g-3">
                        @forelse ($lectures as $lIndex => $lec)
                            @php
                                $lecturerId = $lec['id'] ?? 'lec_' . $lIndex;
                                $lecturerName = data_get($lec, 'user.name', 'Nama Dosen');
                                $slid = $lec['subject_lecture_id'] ?? $lecturerId . '_' . $subjectId; // unik per matkul
                                $cardId = 'card_' . $sid($subjectId) . '_' . $sid($slid);
                                $requiredCount = $questions->where('is_required', true)->count();
                            @endphp

                            <div class="col-12">
                                <div class="card card-lecturer shadow-sm" id="{{ $cardId }}"
                                    data-subject="{{ $subjectId }}" data-slid="{{ $slid }}">
                                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="drag-handle">☰</span>
                                            <div>
                                                <h6 class="mb-0">{{ $lecturerName }}</h6>
                                                <small class="text-muted">Subject Lecture ID: {{ $slid }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-label-success">
                                                Terjawab: <span class="answered-count"
                                                    data-for="{{ $cardId }}">0</span>/<span
                                                    class="required-count">{{ $requiredCount }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        {{-- Loop pertanyaan --}}
                                        <div class="d-grid gap-3">
                                            @forelse ($questions as $i => $q)
                                                @php
                                                    $isChoice = in_array($q->type, ['checkbox', 'option']);
                                                    $opts = ($q->options ?? collect())->sortBy('sequence')->values();
                                                    $nameBase = "answers.{$subjectId}.{$slid}.{$q->id}";
                                                    $oldVal = old(str_replace(['[', ']'], ['.', ''], $nameBase)); // untuk text/radio
                                                @endphp

                                                <div class="question-item card">
                                                    <div class="card-header d-flex align-items-center gap-2 py-2">
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
                                                        <div class="mb-2 fw-medium">{{ $q->question }}</div>

                                                        @if (!$isChoice)
                                                            {{-- TEXT --}}
                                                            <div class="form-floating form-floating-outline"
                                                                style="max-width:560px">
                                                                <input type="text" class="form-control q-control"
                                                                    name="answers[{{ $subjectId }}][{{ $slid }}][{{ $q->id }}]"
                                                                    placeholder="Jawaban singkat"
                                                                    data-required="{{ $q->is_required ? '1' : '' }}"
                                                                    value="{{ old("answers.$subjectId.$slid.$q->id") }}">
                                                                <label>Jawaban singkat</label>
                                                            </div>
                                                        @else
                                                            {{-- RADIO / CHECKBOX --}}
                                                            <div class="mb-2">
                                                                @forelse ($opts as $opt)
                                                                    @php
                                                                        $inputId =
                                                                            'opt_' .
                                                                            $sid($subjectId) .
                                                                            '_' .
                                                                            $sid($slid) .
                                                                            '_' .
                                                                            $q->id .
                                                                            '_' .
                                                                            $opt->id;
                                                                        $isRadio = $q->type === 'option';
                                                                        $checked = $isRadio
                                                                            ? old("answers.$subjectId.$slid.$q->id") ==
                                                                                $opt->id
                                                                            : in_array(
                                                                                $opt->id,
                                                                                (array) old(
                                                                                    "answers.$subjectId.$slid.$q->id",
                                                                                    [],
                                                                                ),
                                                                                true,
                                                                            );
                                                                    @endphp

                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input q-control"
                                                                            type="{{ $isRadio ? 'radio' : 'checkbox' }}"
                                                                            name="answers[{{ $subjectId }}][{{ $slid }}][{{ $q->id }}]{{ $isRadio ? '' : '[]' }}"
                                                                            value="{{ $opt->id }}"
                                                                            id="{{ $inputId }}"
                                                                            data-required="{{ $q->is_required && $isRadio ? '1' : '' }}"
                                                                            {{ $checked ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $inputId }}">{{ $opt->answer }}</label>
                                                                    </div>
                                                                @empty
                                                                    <div class="text-muted fst-italic">Belum ada opsi.</div>
                                                                @endforelse
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="alert alert-warning mb-0">Belum ada pertanyaan pada form ini.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="card-footer text-muted small">
                                        Pastikan semua pertanyaan wajib untuk dosen ini sudah terjawab.
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">Tidak ada dosen terpilih untuk mata kuliah ini.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-primary" onclick="submitEvaluation()">Submit</button>
        </div>
    </form>

    {{-- Toast --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="notificationToast" class="bs-toast toast fade" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="toast-header">
                <i id="toastIcon" class="ri-information-line me-2"></i>
                <div class="me-auto fw-medium" id="toastTitle">Notification</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    {{-- Modal Konfirmasi --}}
    <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSubmitModalLabel">
                        <i class="ri-question-line me-2 text-warning"></i> Konfirmasi Pengisian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="ri-file-check-line text-primary mb-3" style="font-size:3rem;"></i>
                        <h6 class="mb-2">Apakah Anda yakin ingin mengirim jawaban?</h6>
                        <p class="text-muted small mb-0">Setelah dikirim, jawaban tidak dapat diubah lagi. Pastikan semua
                            jawaban sudah benar.</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="submitSpinner"></span>
                        <i class="ri-send-plane-line me-1"></i> Ya, Kirim Jawaban
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
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

        // Recalculate answered required per lecturer card
        function recalcLecturerProgress(card) {
            const requiredControls = card.querySelectorAll('.q-control[data-required="1"]');
            const groups = new Map();

            requiredControls.forEach(ctrl => {
                // radio group: group by name
                if (ctrl.type === 'radio') {
                    groups.set(ctrl.name, (groups.get(ctrl.name) || false) || ctrl.checked);
                } else if (ctrl.type === 'text') {
                    groups.set(ctrl.name, (groups.get(ctrl.name) || false) || ctrl.value.trim() !== '');
                } else {
                    // ignore checkbox for "required" (umumnya pakai radio di evaluasi)
                    groups.set(ctrl.name, (groups.get(ctrl.name) || false));
                }
            });

            let answered = 0;
            groups.forEach(v => {
                if (v) answered++;
            });

            const counter = card.querySelector('.answered-count');
            if (counter) counter.textContent = answered;
            return {
                answered,
                total: groups.size
            };
        }

        function validateAll() {
            const cards = document.querySelectorAll('.card-lecturer');
            let firstInvalid = null;
            cards.forEach(card => {
                const {
                    answered,
                    total
                } = recalcLecturerProgress(card);
                if (answered < total) {
                    card.classList.add('invalid');
                    if (!firstInvalid) firstInvalid = card;
                } else {
                    card.classList.remove('invalid');
                }
            });
            return firstInvalid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // init progress
            document.querySelectorAll('.card-lecturer').forEach(card => recalcLecturerProgress(card));

            // listeners
            document.querySelectorAll('.q-control').forEach(ctrl => {
                ctrl.addEventListener('input', function() {
                    const card = this.closest('.card-lecturer');
                    if (card) recalcLecturerProgress(card);
                });
                ctrl.addEventListener('change', function() {
                    const card = this.closest('.card-lecturer');
                    if (card) recalcLecturerProgress(card);
                });
            });

            // confirm submit
            document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
                const btn = this;
                const spinner = document.getElementById('submitSpinner');
                btn.disabled = true;
                spinner.classList.remove('d-none');
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Mengumpulkan...';
                setTimeout(() => {
                    document.getElementById('answerForm').submit();
                }, 400);
            });
        });

        function submitEvaluation() {
            const firstInvalid = validateAll();
            if (firstInvalid) {
                showNotification('Masih ada dosen dengan pertanyaan wajib yang belum terjawab.', 'warning');
                // pindah ke tab matkul terkait
                const pane = firstInvalid.closest('.tab-pane');
                if (pane && !pane.classList.contains('active')) {
                    const tabBtn = document.querySelector(`[data-bs-target="#${pane.id}"]`);
                    if (tabBtn) new bootstrap.Tab(tabBtn).show();
                }
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
            confirmModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const scroller = document.getElementById('subjectTabs');
            if (!scroller) return;

            // Buat indicator jika belum ada
            let indicator = scroller.querySelector('.tab-active-indicator');
            if (!indicator) {
                indicator = document.createElement('span');
                indicator.className = 'tab-active-indicator';
                scroller.appendChild(indicator);
            }

            function updateIndicator() {
                const activeLink = scroller.querySelector('.nav-link.active');
                if (!activeLink) return;

                // Ambil LI biar width-nya konsisten dengan padding tab
                const li = activeLink.closest('li') || activeLink;

                // Posisi & lebar relatif ke konten UL (bukan viewport)
                const left = li.offsetLeft;
                const width = li.offsetWidth;

                indicator.style.left = left + 'px';
                indicator.style.width = width + 'px';
            }

            function centerTab(el) {
                const cRect = scroller.getBoundingClientRect();
                const eRect = el.getBoundingClientRect();
                // Delta untuk memusatkan el di dalam scroller
                const delta = (eRect.left - cRect.left) - (cRect.width - eRect.width) / 2;
                scroller.scrollTo({
                    left: scroller.scrollLeft + delta,
                    behavior: 'smooth'
                });
            }

            // Inisialisasi posisi indicator & center tab aktif saat load
            const initialActive = scroller.querySelector('.nav-link.active');
            if (initialActive) {
                updateIndicator();
                // opsional: center saat load
                // centerTab(initialActive);
            }

            // Saat tab di-activate oleh Bootstrap
            scroller.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('shown.bs.tab', (e) => {
                    const el = e.target;
                    // Center lalu update indicator
                    centerTab(el);
                    // Tunggu layout settle sedikit sebelum set indikator (opsional)
                    requestAnimationFrame(updateIndicator);
                });

                // Geser dulu sebelum transisi selesai agar indikator tidak tertinggal
                link.addEventListener('click', (e) => {
                    centerTab(e.currentTarget);
                });
            });

            // Resize => lebar tab berubah => update indikator
            window.addEventListener('resize', updateIndicator);

            // Tidak perlu update saat scroll: indikator anak dari UL,
            // jadi ikut bergeser otomatis bersama konten.
        });
    </script>
@endsection
