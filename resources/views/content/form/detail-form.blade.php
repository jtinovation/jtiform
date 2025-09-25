@extends('layouts/contentNavbarLayout')

@section('title', 'Hasil Pengerjaan')

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

        .list-group-item-selected {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
    </style>
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.active') }}">Form</a></li>
            <li class="breadcrumb-item active">Hasil Pengerjaan</li>
        </ol>
    </nav>

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
                            <div class="mt-2 text-muted small">
                                <div>Mulai mengerjakan: {{ \Carbon\Carbon::parse($submission->started_at)->format('d M Y H:i') }}</div>
                                <div>Selesai: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('form.active') }}"
                                class="btn btn-outline-secondary">Kembali</a>
                        </div>
                    </div>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="badge bg-label-primary">Total Pertanyaan: {{ $questions->count() }}</span>
                        <span class="badge bg-label-success">Total Skor: {{ $answers->sum('score') }}</span>
                        <span class="badge bg-label-info">Dijawab: {{ $answers->count() }}/{{ $questions->count() }}</span>
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

                        // Cari jawaban untuk pertanyaan ini dari collection answers
                        $userAnswer = $answers->firstWhere('m_question_id', $q->id);

                        // Untuk text answer
                        $textAnswer = $userAnswer ? $userAnswer->text_value : null;

                        // Untuk option answer (radio button)
                        $selectedOptionId = $userAnswer ? $userAnswer->m_question_option_id : null;

                        // Untuk checkbox answer (multiple selection) - dari answerOptions
                        $selectedOptionIds = [];
                        if ($q->type === 'checkbox' && $userAnswer && $userAnswer->answerOptions) {
                            $selectedOptionIds = $userAnswer->answerOptions->pluck('m_question_option_id')->toArray();
                        }
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
                                {{-- Tipe Text: tampilkan jawaban user --}}
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Jawaban:</label>
                                    @if ($textAnswer)
                                        <div class="alert alert-primary mb-0">
                                            <i class="bx bx-check-circle me-1"></i>
                                            {{ $textAnswer }}
                                            @if ($userAnswer && $userAnswer->score > 0)
                                                <span class="badge bg-primary ms-2">Skor: {{ $userAnswer->score }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <i class="bx bx-x-circle me-1"></i>
                                            Tidak dijawab
                                        </div>
                                    @endif
                                </div>
                            @else
                                {{-- Tipe Checkbox/Option: tampilkan list opsi + point --}}
                                <div class="mb-2">
                                    <div class="row fw-medium text-muted px-1 mb-1">
                                        <div class="col-auto opt-seq">#</div>
                                        <div class="col">Pilihan Jawaban</div>
                                        <div class="col-2">Point</div>
                                    </div>

                                    <div class="list-group">
                                        @forelse($opts as $j => $opt)
                                            @php
                                                $isSelected = false;
                                                if ($q->type === 'option') {
                                                    $isSelected = ($selectedOptionId == $opt->id);
                                                } else if ($q->type === 'checkbox') {
                                                    $isSelected = in_array($opt->id, $selectedOptionIds);
                                                }
                                            @endphp
                                            <div class="list-group-item d-flex align-items-center gap-2 {{ $isSelected ? 'list-group-item-selected' : '' }}">
                                                <span class="text-muted small opt-seq">{{ $j + 1 }}</span>
                                                @if ($q->type === 'checkbox')
                                                    <i class="bx {{ $isSelected ? 'bx-check-square text-primary' : 'bx-square' }} me-2"></i>
                                                @else
                                                    <i class="bx {{ $isSelected ? 'bx-radio-circle-marked text-primary' : 'bx-radio-circle' }} me-2"></i>
                                                @endif
                                                <div class="flex-grow-1">
                                                    {{ $opt->answer }}
                                                    @if ($isSelected)
                                                        <span class="badge bg-primary ms-2">Dipilih</span>
                                                    @endif
                                                </div>
                                                <div class="col-2">
                                                    <span class="badge {{ $isSelected ? 'bg-primary' : 'bg-label-primary' }} w-100 text-start">
                                                        Point: {{ (int) $opt->point }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted fst-italic">Belum ada opsi.</div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- hint jenis pilihan --}}
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
@endsection
