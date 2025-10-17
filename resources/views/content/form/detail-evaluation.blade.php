@extends('layouts/contentNavbarLayout')
@section('title', 'Hasil Pengerjaan')

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

        .list-group-item-selected {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
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
            <li class="breadcrumb-item"><a href="{{ route('form.history', $form->id) }}">Form</a></li>
            <li class="breadcrumb-item active">Hasil Pengerjaan</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap align-items-start gap-3">
            <div class="flex-grow-1">
                <h4 class="mb-1">{{ $form->title ?? 'Tanpa Judul' }}</h4>
                @if (!empty($form->description))
                    <p class="mb-0 text-muted">{{ $form->description }}</p>
                @endif
                <div class="mt-2 text-muted small">
                    <div>Tanggal Submit: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y H:i') }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('form.history') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
        <div class="card-body pt-0">
            <span class="badge bg-label-primary">Total Pertanyaan: {{ $questions->count() }}</span>
        </div>
    </div>

    @php
        $sid = fn($s) => preg_replace('/[^A-Za-z0-9_-]/', '_', $s);
    @endphp

    {{-- Tabs mata kuliah --}}
    <ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" id="subjectTabs" role="tablist" style="white-space: nowrap;">
        @foreach ($subjectsView as $i => $sub)
            @php
                $subject = $sub['subject'];
                $tabId = 'sub_' . $sid($subject['id']);
                $lectCount = count($sub['lectures'] ?? []);
            @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $i === 0 ? 'active' : '' }}" id="{{ $tabId }}-tab" data-bs-toggle="tab"
                    data-bs-target="#{{ $tabId }}" type="button" role="tab" aria-controls="{{ $tabId }}"
                    aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                    {{ $subject['name'] }} <span class="text-muted">({{ $subject['code'] }})</span>
                    <span class="badge bg-label-info ms-1">{{ $lectCount }} dosen</span>
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="subjectTabsContent">
        @foreach ($subjectsView as $i => $sub)
            @php
                $subject = $sub['subject'];
                $tabId = 'sub_' . $sid($subject['id']);
                $lectures = $sub['lectures'] ?? [];
            @endphp

            <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel"
                aria-labelledby="{{ $tabId }}-tab">
                <div class="row g-3">
                    @forelse ($lectures as $lec)
                        @php
                            $slid = $lec['subject_lecture_id'];
                            $lecName = $lec['lecturer_name'];
                            $byQ = $lec['answers_by_qid']; // qid => ['type','text','sel_id','sel_ids','score']
                            // hitung answered required untuk badge progress
                            $requiredTotal = $questions->where('is_required', true)->count();
                            $answeredReq = 0;
                            foreach ($questions as $q) {
                                if (!$q->is_required) {
                                    continue;
                                }
                                $row = $byQ[$q->id] ?? null;
                                if (!$row) {
                                    continue;
                                }
                                if ($q->type === 'text' && filled($row['text'])) {
                                    $answeredReq++;
                                }
                                if ($q->type === 'option' && filled($row['sel_id'])) {
                                    $answeredReq++;
                                }
                                if ($q->type === 'checkbox' && !empty($row['sel_ids'])) {
                                    $answeredReq++;
                                }
                            }
                        @endphp

                        <div class="col-12">
                            <div class="card card-lecturer shadow-sm">
                                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="drag-handle">☰</span>
                                        <div>
                                            <h6 class="mb-0">{{ $lecName }}</h6>
                                            <small class="text-muted">Subject Lecture ID: {{ $slid }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-success">
                                            Terjawab: {{ $answeredReq }}/{{ $requiredTotal }}
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="d-grid gap-3">
                                        @forelse ($questions as $idx => $q)
                                            @php
                                                $row = $byQ[$q->id] ?? null;
                                                $isChoice = in_array($q->type, ['checkbox', 'option']);
                                                $opts = ($q->options ?? collect())->sortBy('sequence')->values();
                                                $selId = $row['sel_id'] ?? null;
                                                $selIds = $row['sel_ids'] ?? [];
                                                $text = $row['text'] ?? null;
                                                $score = (int) ($row['score'] ?? 0);
                                            @endphp

                                            <div class="question-item card">
                                                <div class="card-header d-flex align-items-center gap-2 py-2">
                                                    <strong class="me-auto">Pertanyaan {{ $idx + 1 }}</strong>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge badge-soft badge-type">
                                                            {{ $q->type === 'text' ? 'Text' : ($q->type === 'checkbox' ? 'Checkbox' : 'Option') }}
                                                        </span>
                                                        @if ($q->is_required)
                                                            <span class="badge bg-label-danger">Wajib</span>
                                                        @else
                                                            <span class="badge bg-label-secondary">Opsional</span>
                                                        @endif
                                                        <span class="badge bg-label-primary">Skor:
                                                            {{ $score }}</span>
                                                    </div>
                                                </div>

                                                <div class="card-body pt-3">
                                                    <div class="mb-3">
                                                        <div class="fw-medium">{{ $q->question }}</div>
                                                    </div>

                                                    @if (!$isChoice)
                                                        {{-- TEXT --}}
                                                        <div class="mb-3">
                                                            <label class="form-label fw-medium">Jawaban:</label>
                                                            @if (filled($text))
                                                                <div class="alert alert-primary mb-0">
                                                                    <i class="bx bx-check-circle me-1"></i>
                                                                    {{ $text }}
                                                                </div>
                                                            @else
                                                                <div class="alert alert-warning mb-0">
                                                                    <i class="bx bx-x-circle me-1"></i> Tidak dijawab
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        {{-- RADIO / CHECKBOX --}}
                                                        <div class="mb-2">
                                                            <div class="row fw-medium text-muted px-1 mb-1">
                                                                <div class="col-auto opt-seq">#</div>
                                                                <div class="col">Pilihan Jawaban</div>
                                                                <div class="col-2">Point</div>
                                                            </div>
                                                            <div class="list-group">
                                                                @forelse ($opts as $j => $opt)
                                                                    @php
                                                                        $isSelected =
                                                                            $q->type === 'option'
                                                                                ? $selId == $opt->id
                                                                                : in_array($opt->id, $selIds, true);
                                                                    @endphp
                                                                    <div
                                                                        class="list-group-item d-flex align-items-center gap-2 {{ $isSelected ? 'list-group-item-selected' : '' }}">
                                                                        <span
                                                                            class="text-muted small opt-seq">{{ $j + 1 }}</span>
                                                                        @if ($q->type === 'checkbox')
                                                                            <i
                                                                                class="bx {{ $isSelected ? 'bx-check-square text-primary' : 'bx-square' }} me-2"></i>
                                                                        @else
                                                                            <i
                                                                                class="bx {{ $isSelected ? 'bx-radio-circle-marked text-primary' : 'bx-radio-circle' }} me-2"></i>
                                                                        @endif
                                                                        <div class="flex-grow-1">
                                                                            {{ $opt->answer }}
                                                                            @if ($isSelected)
                                                                                <span
                                                                                    class="badge bg-primary ms-2">Dipilih</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <span
                                                                                class="badge {{ $isSelected ? 'bg-primary' : 'bg-label-primary' }} w-100 text-start">
                                                                                Point: {{ (int) $opt->point }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <div class="text-muted fst-italic">Belum ada opsi.</div>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                        <div class="text-muted small">
                                                            Jenis pilihan:
                                                            <strong>{{ $q->type === 'checkbox' ? 'Multi jawaban (checkbox)' : 'Satu jawaban (radio)' }}</strong>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="alert alert-warning mb-0">Belum ada pertanyaan pada form ini.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">Tidak ada dosen pada mata kuliah ini.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('page-script')
    <script>
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
