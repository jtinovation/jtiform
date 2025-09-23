@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Pertanyaan (Dinamis)')

@section('page-style')
    <style>
        .drag-handle {
            cursor: grab;
            user-select: none;
            font-size: 18px;
            line-height: 1;
            padding: 0 .25rem
        }

        .question-item.sortable-chosen {
            background: #f7f7f9
        }

        .question-item .card-header {
            background: #fafafa
        }

        .option-row .remove-option {
            visibility: hidden
        }

        .option-row:hover .remove-option {
            visibility: visible
        }

        .option-row .opt-drag {
            cursor: grab
        }

        .opt-seq {
            min-width: 2.25rem;
            text-align: center
        }
    </style>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const $list = $('#question-list');
            const $removedBins = $('#removed-bins');
            let counter = Number($list.data('start-counter')) || 0; // start index untuk pertanyaan baru

            // Sortable untuk kartu pertanyaan
            Sortable.create(document.getElementById('question-list'), {
                handle: '.drag-handle',
                animation: 150,
                onSort: refreshSequencesAndNumbers
            });

            // Inisialisasi Sortable untuk setiap opsi existing
            $list.find('.question-item').each(function() {
                initOptionsSortable($(this));
            });
            refreshSequencesAndNumbers();

            // ==== EVENTS ====
            $('#btn-add-question').on('click', function(e) {
                e.preventDefault();
                addQuestion();
            });

            // Hapus pertanyaan
            $list.on('click', '.btn-remove-question', function(e) {
                e.preventDefault();
                const $card = $(this).closest('.question-item');
                const existingId = $card.data('existing-id');
                if (existingId) {
                    // tandai untuk delete
                    $removedBins.append(
                        `<input type="hidden" name="removed_question_ids[]" value="${existingId}">`);
                }
                $card.remove();
                refreshSequencesAndNumbers();
                if ($list.children('.question-item').length === 0) addQuestion();
            });

            // Ubah tipe -> tampil/sembunyikan opsi
            $list.on('change', '.field-type', function() {
                const $card = $(this).closest('.question-item');
                toggleOptionsBox($card);
            });

            // Tambah opsi
            $list.on('click', '.btn-add-option', function(e) {
                e.preventDefault();
                const $card = $(this).closest('.question-item');
                addOptionRow($card);
                renumberOptionFields($card);
            });

            // Hapus opsi
            $list.on('click', '.remove-option', function(e) {
                e.preventDefault();
                const $row = $(this).closest('.option-row');
                const $card = $(this).closest('.question-item');
                const existingId = $row.data('existing-id');
                if (existingId) {
                    $removedBins.append(
                        `<input type="hidden" name="removed_option_ids[]" value="${existingId}">`);
                }
                $row.remove();

                const $wrap = $card.find('.options-wrapper');
                if ($wrap.children('.option-row').length === 0) addOptionRow($card);

                renumberOptionFields($card);
            });

            // Sebelum submit: set sequence pertanyaan & opsi
            $('#form-edit-questions').on('submit', function() {
                refreshSequencesAndNumbers();
                $('#question-list .question-item').each(function() {
                    renumberOptionFields($(this));
                    // normalisasi is_required agar kirim 0 kalau unchecked (opsional; bisa juga handle di server)
                    const $req = $(this).find('.field-required');
                    if (!$req.is(':checked')) {
                        // tambahkan hidden 0 supaya mudah diproses
                        const name = $req.attr('name');
                        $(this).append(`<input type="hidden" name="${name}" value="0">`);
                    }
                });
            });

            // ==== HELPERS ====
            function addQuestion(prefill = {}) {
                const idx = counter++;
                const formId = @json($form->id);
                const html = `
      <div class="question-item card shadow-sm" data-index="${idx}">
        <div class="card-header d-flex align-items-center gap-2 py-2">
          <span class="drag-handle" title="Seret untuk mengurutkan">☰</span>
          <strong class="me-auto">Pertanyaan <span class="q-number"></span></strong>
          <button class="btn btn-sm btn-outline-danger btn-remove-question" type="button">Hapus</button>
        </div>

        <div class="card-body pt-3">
          <input type="hidden" name="questions[${idx}][m_form_id]" value="${formId}">
          <input type="hidden" class="field-sequence" name="questions[${idx}][sequence]" value="0">
          <!-- tidak ada [id] karena pertanyaan baru -->

          <div class="row g-4">
            <div class="col-md-7">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control field-question" name="questions[${idx}][question]" placeholder="Tulis pertanyaan" value="" required>
                <label>Pertanyaan</label>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-floating form-floating-outline">
                <select class="form-select field-type" name="questions[${idx}][type]" required>
                  <option value="" selected disabled>-- Pilih Tipe --</option>
                  <option value="text">Text</option>
                  <option value="checkbox">Checkbox (multi)</option>
                  <option value="option">Option (single)</option>
                </select>
                <label>Tipe</label>
              </div>
            </div>

            <div class="col-md-2 d-flex align-items-center">
              <div class="form-check form-switch">
                <input class="form-check-input field-required" type="checkbox" name="questions[${idx}][is_required]" value="1">
                <label class="form-check-label">Wajib</label>
              </div>
            </div>
          </div>

          <div class="mt-4 options-box" style="display:none">
            <div class="d-flex align-items-center mb-2">
              <h6 class="mb-0 me-auto">Pilihan Jawaban</h6>
              <button type="button" class="btn btn-sm btn-outline-primary btn-add-option">+ Tambah Opsi</button>
            </div>

            <div class="row fw-medium text-muted px-1 mb-1">
              <div class="col-auto opt-seq">#</div>
              <div class="col">Teks Opsi</div>
              <div class="col-2">Point</div>
              <div class="col-auto" style="width:3rem;"></div>
            </div>

            <div class="options-wrapper list-group"></div>
          </div>
        </div>
      </div>
    `;
                $list.append(html);
                const $card = $list.children('.question-item').last();

                toggleOptionsBox($card); // default hidden
                initOptionsSortable($card); // siapkan sortable opsi
                refreshSequencesAndNumbers();
            }

            function addOptionRow($card, defaultLabel = '', defaultPoint = 0, existingOptId = null) {
                const idx = Number($card.attr('data-index'));
                const $wrap = $card.find('.options-wrapper');
                const optionIdx = $wrap.children('.option-row').length;

                const row = `
      <div class="option-row list-group-item d-flex align-items-center gap-2" ${existingOptId ? `data-existing-id="${existingOptId}"` : ''}>
        <span class="opt-drag" title="Seret">⋮⋮</span>
        <div class="opt-seq small text-muted">1</div>

        <input type="hidden" class="field-opt-sequence" name="questions[${idx}][options][${optionIdx}][sequence]" value="${optionIdx+1}">
        ${existingOptId ? `<input type="hidden" name="questions[${idx}][options][${optionIdx}][id]" value="${existingOptId}">` : ''}

        <div class="flex-grow-1">
          <input type="text" class="form-control"
            name="questions[${idx}][options][${optionIdx}][label]"
            placeholder="Teks opsi" value="${escapeHtml(defaultLabel)}" required>
        </div>

        <div class="col-2">
          <input type="number" class="form-control"
            name="questions[${idx}][options][${optionIdx}][point]"
            placeholder="0" value="${Number(defaultPoint) || 0}" step="1" min="0">
        </div>

        <button class="btn btn-outline-secondary remove-option" type="button" aria-label="Hapus opsi">&times;</button>
      </div>
    `;
                $wrap.append(row);
            }

            function initOptionsSortable($card) {
                const wrapEl = $card.find('.options-wrapper').get(0);
                if (!wrapEl || wrapEl.__sortable_inited) return;
                wrapEl.__sortable_inited = true;

                Sortable.create(wrapEl, {
                    handle: '.opt-drag',
                    animation: 150,
                    onSort: () => renumberOptionFields($card)
                });
            }

            function toggleOptionsBox($card) {
                const typeVal = $card.find('.field-type').val();
                const $box = $card.find('.options-box');
                if (typeVal === 'checkbox' || typeVal === 'option') {
                    $box.slideDown(120, () => initOptionsSortable($card));
                    const $wrap = $card.find('.options-wrapper');
                    if ($wrap.children('.option-row').length === 0) {
                        addOptionRow($card, 'Opsi 1', 0);
                        addOptionRow($card, 'Opsi 2', 0);
                    }
                    renumberOptionFields($card);
                } else {
                    $box.slideUp(120);
                }
            }

            function renumberOptionFields($card) {
                const idx = Number($card.attr('data-index'));
                const $rows = $card.find('.options-wrapper .option-row');

                $rows.each(function(i) {
                    const $row = $(this);
                    $row.find('.opt-seq').text(i + 1);
                    // sequence
                    $row.find('.field-opt-sequence')
                        .attr('name', `questions[${idx}][options][${i}][sequence]`)
                        .val(i + 1);
                    // id (kalau ada)
                    const existingId = $row.data('existing-id');
                    if (existingId) {
                        let $idInput = $row.find('input[name$="[id]"]');
                        if ($idInput.length === 0) {
                            $row.append(
                                `<input type="hidden" name="questions[${idx}][options][${i}][id]" value="${existingId}">`
                            );
                        } else {
                            $idInput.attr('name', `questions[${idx}][options][${i}][id]`);
                        }
                    }
                    // label
                    $row.find('input[type="text"]').attr('name', `questions[${idx}][options][${i}][label]`);
                    // point
                    $row.find('input[type="number"]').attr('name',
                        `questions[${idx}][options][${i}][point]`);
                });
            }

            function refreshSequencesAndNumbers() {
                $('#question-list .question-item').each(function(i) {
                    $(this).find('.q-number').text(i + 1);
                    $(this).find('.field-sequence').val(i + 1);
                });
            }

            function escapeHtml(str) {
                if (str == null) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        });
    </script>
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.index') }}">Management Form</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.question.index', $form->id) }}">List Pertanyaan</a></li>
            <li class="breadcrumb-item active">Edit Pertanyaan</li>
        </ol>
    </nav>

    <form id="form-edit-questions" action="{{ route('form.question.update', $form->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3 d-flex gap-2">
            <button class="btn btn-primary" id="btn-add-question" type="button">+ Tambah Pertanyaan</button>
            <a href="{{ route('form.question.index', $form->id) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>

        {{-- wadah input hidden untuk ID yang dihapus --}}
        <div id="removed-bins"></div>

        <div id="question-list" class="d-grid gap-3" data-start-counter="{{ $questions->count() }}">
            {{-- Render pertanyaan & opsi existing --}}
            @foreach ($questions as $qIndex => $q)
                <div class="question-item card shadow-sm" data-index="{{ $qIndex }}"
                    data-existing-id="{{ $q->id }}">
                    <div class="card-header d-flex align-items-center gap-2 py-2">
                        <span class="drag-handle" title="Seret untuk mengurutkan">☰</span>
                        <strong class="me-auto">Pertanyaan <span class="q-number"></span></strong>
                        <button class="btn btn-sm btn-outline-danger btn-remove-question" type="button">Hapus</button>
                    </div>

                    <div class="card-body pt-3">
                        <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $q->id }}">
                        <input type="hidden" name="questions[{{ $qIndex }}][m_form_id]"
                            value="{{ $form->id }}">
                        <input type="hidden" class="field-sequence" name="questions[{{ $qIndex }}][sequence]"
                            value="{{ $q->sequence ?? $qIndex + 1 }}">

                        <div class="row g-4">
                            <div class="col-md-7">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control field-question"
                                        name="questions[{{ $qIndex }}][question]" value="{{ $q->question }}"
                                        placeholder="Tulis pertanyaan" required>
                                    <label>Pertanyaan</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select field-type" name="questions[{{ $qIndex }}][type]"
                                        required>
                                        <option value="" disabled>-- Pilih Tipe --</option>
                                        <option value="text" {{ $q->type === 'text' ? 'selected' : '' }}>Text</option>
                                        <option value="checkbox" {{ $q->type === 'checkbox' ? 'selected' : '' }}>Checkbox
                                            (multi)</option>
                                        <option value="option" {{ $q->type === 'option' ? 'selected' : '' }}>Option
                                            (single)</option>
                                    </select>
                                    <label>Tipe</label>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input field-required" type="checkbox"
                                        name="questions[{{ $qIndex }}][is_required]" value="1"
                                        {{ $q->is_required ? 'checked' : '' }}>
                                    <label class="form-check-label">Wajib</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 options-box"
                            style="{{ in_array($q->type, ['checkbox', 'option']) ? '' : 'display:none' }}">
                            <div class="d-flex align-items-center mb-2">
                                <h6 class="mb-0 me-auto">Pilihan Jawaban</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-add-option">+ Tambah
                                    Opsi</button>
                            </div>

                            <div class="row fw-medium text-muted px-1 mb-1">
                                <div class="col-auto opt-seq">#</div>
                                <div class="col">Teks Opsi</div>
                                <div class="col-2">Point</div>
                                <div class="col-auto" style="width:3rem;"></div>
                            </div>

                            <div class="options-wrapper list-group">
                                @php
                                    $opts = ($q->options ?? collect())->sortBy('sequence')->values();
                                @endphp
                                @foreach ($opts as $oIndex => $opt)
                                    <div class="option-row list-group-item d-flex align-items-center gap-2"
                                        data-existing-id="{{ $opt->id }}">
                                        <span class="opt-drag" title="Seret">⋮⋮</span>
                                        <div class="opt-seq small text-muted">{{ $oIndex + 1 }}</div>

                                        <input type="hidden" class="field-opt-sequence"
                                            name="questions[{{ $qIndex }}][options][{{ $oIndex }}][sequence]"
                                            value="{{ $opt->sequence ?? $oIndex + 1 }}">
                                        <input type="hidden"
                                            name="questions[{{ $qIndex }}][options][{{ $oIndex }}][id]"
                                            value="{{ $opt->id }}">

                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control"
                                                name="questions[{{ $qIndex }}][options][{{ $oIndex }}][label]"
                                                value="{{ $opt->answer }}" placeholder="Teks opsi" required>
                                        </div>

                                        <div class="col-2">
                                            <input type="number" class="form-control"
                                                name="questions[{{ $qIndex }}][options][{{ $oIndex }}][point]"
                                                value="{{ $opt->point ?? 0 }}" placeholder="0" step="1"
                                                min="0">
                                        </div>

                                        <button class="btn btn-outline-secondary remove-option" type="button"
                                            aria-label="Hapus opsi">&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
@endsection
