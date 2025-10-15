@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Form')

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
            <li class="breadcrumb-item"><a href="{{ route('form.index') }}">Management Form</a></li>
            <li class="breadcrumb-item active">Detail Form</li>
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
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            {{-- <a href="{{ route('form.question.index', $form->id) }}"
                                class="btn btn-outline-secondary">Kembali</a> --}}
                            <a href="{{ route('form.question.edit', $form->id) }}" class="btn btn-primary">Edit
                                Pertanyaan</a>
                            <a href="{{ route('form.summary', $form->id) }}" class="btn btn-primary">Lihat
                                Ringkasan</a>
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
                                <div class="text-muted small">Urutan: {{ $q->sequence }}</div>
                            </div>

                            {{-- Tampilan jawaban sesuai tipe --}}
                            @if (!$isChoice)
                                {{-- Tipe Text: tampilkan mock input disabled --}}
                                <div class="form-floating form-floating-outline" style="max-width:560px">
                                    <input type="text" class="form-control" placeholder="Jawaban singkat" disabled>
                                    <label>Jawaban singkat</label>
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
                                            <div class="list-group-item d-flex align-items-center gap-2">
                                                <span class="text-muted small opt-seq">{{ $j + 1 }}</span>
                                                <div class="flex-grow-1">
                                                    {{ $opt->answer }}
                                                </div>
                                                <div class="col-2">
                                                    <span class="badge bg-label-primary w-100 text-start">Point:
                                                        {{ (int) $opt->point }}</span>
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
