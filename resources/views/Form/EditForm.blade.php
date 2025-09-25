@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Kuesioner: {{ $form->title }}</h2>

    {{-- Update data form utama --}}
    <form action="{{ route('form.update', $form->id) }}" method="POST" class="mb-4">
        @csrf
        @method('PUT')

        {{-- Kode Form --}}
        <div class="mb-3">
            <label for="code">Kode Form</label>
            <input type="text" name="code" value="{{ $form->code }}" class="form-control" required>
        </div>

        {{-- Judul Form --}}
        <div class="mb-3">
            <label for="title">Judul Form</label>
            <input type="text" name="title" value="{{ $form->title }}" class="form-control" required>
        </div>

        {{-- Tipe Form --}}
        <div class="mb-3">
            <label for="description">Tipe Form</label>
            <select name="description" class="form-control" required>
                <option value="survei" {{ $form->description == 'survei' ? 'selected' : '' }}>Survei</option>
                <option value="kuesioner" {{ $form->description == 'kuesioner' ? 'selected' : '' }}>Kuesioner</option>
            </select>
        </div>

        {{-- Tanggal Mulai --}}
        <div class="mb-3">
            <label for="start_at">Tanggal Mulai</label>
            <input type="date" name="start_at" value="{{ $form->start_at->format('Y-m-d') }}" class="form-control" required>
        </div>

        {{-- Tanggal Selesai --}}
        <div class="mb-3">
            <label for="end_at">Tanggal Selesai</label>
            <input type="date" name="end_at" value="{{ $form->end_at->format('Y-m-d') }}" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Update Form</button>
    </form>

    <hr>

    {{-- Tambah Pertanyaan --}}
    <h4>Pertanyaan</h4>
    <form action="{{ route('form.update', $form->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div id="questions-container">
            @foreach($form->questions as $index => $question)
                <div class="question-item mb-3 d-flex gap-2 align-items-start">
                    {{-- Pertanyaan --}}
                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                    <input type="text" name="questions[{{ $index }}][text]"
                           value="{{ $question->text }}"
                           placeholder="Pertanyaan"
                           class="form-control w-25" required>

                    {{-- Tipe Pertanyaan --}}
                    <select name="questions[{{ $index }}][type]"
                            class="form-control w-25 question-type"
                            data-index="{{ $index }}" required>
                        <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="option" {{ $question->type == 'option' ? 'selected' : '' }}>Option</option>
                        <option value="checkbox" {{ $question->type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                    </select>

                    {{-- Jawaban Dinamis --}}
                    <div class="answers-container w-50">
                        @if($question->type === 'text')
                            <input type="text" name="questions[{{ $index }}][answers][]"
                                   class="form-control mb-2" placeholder="Jawaban...">
                        @else
                            @foreach($question->options as $optIndex => $option)
                                <div class="d-flex gap-2 mb-2 option-row">
                                    <input type="text" name="questions[{{ $index }}][answers][]"
                                           value="{{ $option->answer }}" class="form-control" placeholder="Opsi {{ chr(65 + $optIndex) }}">
                                    <button type="button" class="btn btn-danger btn-sm remove-option">X</button>
                                </div>
                            @endforeach
                            <button type="button" class="btn btn-secondary btn-sm add-option" data-index="{{ $index }}">Tambah Opsi</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-question" class="btn btn-secondary mb-3">Tambah Pertanyaan</button>
        <br>
        <button type="submit" class="btn btn-primary">Simpan Pertanyaan</button>
    </form>
</div>

{{-- Script Dinamis --}}
<script>
let questionIndex = {{ $form->questions->count() }};

// Tambah Pertanyaan Baru
document.getElementById('add-question').addEventListener('click', function() {
    const container = document.getElementById('questions-container');
    const html = `
        <div class="question-item mb-3 d-flex gap-2 align-items-start">
            <input type="text" name="questions[${questionIndex}][text]" placeholder="Pertanyaan" class="form-control w-25" required>
            <select name="questions[${questionIndex}][type]" class="form-control w-25 question-type" data-index="${questionIndex}" required>
                <option value="text">Text</option>
                <option value="option">Option</option>
                <option value="checkbox">Checkbox</option>
            </select>
            <div class="answers-container w-50">
                <input type="text" name="questions[${questionIndex}][answers][]" class="form-control mb-2" placeholder="Jawaban...">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    questionIndex++;
});

// Tambah Opsi Baru (dinamis)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-option')) {
        const index = e.target.getAttribute('data-index');
        const answersContainer = e.target.closest('.answers-container');

        // Cari jumlah opsi yang sudah ada
        const optionCount = answersContainer.querySelectorAll('.option-row').length;
        const label = String.fromCharCode(65 + optionCount); // A, B, C, D, dst

        const html = `
            <div class="d-flex gap-2 mb-2 option-row">
                <input type="text" name="questions[${index}][answers][]" class="form-control" placeholder="Opsi ${label}">
                <button type="button" class="btn btn-danger btn-sm remove-option">X</button>
            </div>
        `;
        e.target.insertAdjacentHTML('beforebegin', html);
    }

    // Hapus Opsi
    if (e.target.classList.contains('remove-option')) {
        e.target.closest('.option-row').remove();
    }
});

// Ganti tampilan jawaban saat ubah tipe
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('question-type')) {
        const index = e.target.getAttribute('data-index');
        const container = e.target.closest('.question-item').querySelector('.answers-container');

        if (e.target.value === 'text') {
            // Jika Text, hanya satu input jawaban
            container.innerHTML = `<input type="text" name="questions[${index}][answers][]" class="form-control mb-2" placeholder="Jawaban...">`;
        } else {
            // Jika Option / Checkbox, default 2 opsi pertama
            container.innerHTML = `
                <div class="d-flex gap-2 mb-2 option-row">
                    <input type="text" name="questions[${index}][answers][]" class="form-control" placeholder="Opsi A">
                    <button type="button" class="btn btn-danger btn-sm remove-option">X</button>
                </div>
                <div class="d-flex gap-2 mb-2 option-row">
                    <input type="text" name="questions[${index}][answers][]" class="form-control" placeholder="Opsi B">
                    <button type="button" class="btn btn-danger btn-sm remove-option">X</button>
                </div>
                <button type="button" class="btn btn-secondary btn-sm add-option" data-index="${index}">Tambah Opsi</button>
            `;
        }
    }
});
</script>
@endsection
