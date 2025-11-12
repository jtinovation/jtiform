@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Pertanyaan (Dinamis + Sortable Option + Point)')

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
            let $list = $('#question-list');
            let counter = 0;

            Sortable.create(document.getElementById('question-list'), {
                handle: '.drag-handle',
                animation: 150,
                onSort: refreshSequencesAndNumbers
            });

            // --- EVENT LISTENERS ---
            addQuestion();

            $('#btn-add-question').on('click', function(e) {
                e.preventDefault();
                addQuestion();
            });

            // Hapus pertanyaan
            $list.on('click', '.btn-remove-question', function(e) {
                e.preventDefault();
                $(this).closest('.question-item').remove();
                refreshSequencesAndNumbers();
                if ($list.children('.question-item').length === 0) addQuestion();
            });

            // CLONE pertanyaan
            $list.on('click', '.btn-clone-question', function(e) {
                e.preventDefault();
                const $card = $(this).closest('.question-item');
                cloneQuestion($card);
            });

            // Ganti tipe pertanyaan (tampilkan/sembunyikan opsi)
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
                const $card = $(this).closest('.question-item');
                const $wrap = $card.find('.options-wrapper');
                $(this).closest('.option-row').remove();
                if ($wrap.children('.option-row').length === 0) addOptionRow($card);
                renumberOptionFields($card);
            });

            // Submit form: pastikan sequence question & option sudah benar
            $('#form-create-questions').on('submit', function() {
                refreshSequencesAndNumbers();
                $('#question-list .question-item').each(function() {
                    renumberOptionFields($(this));
                });
            });

            // --- FUNGSI: TAMBAH PERTANYAAN ---
            function addQuestion(prefill = {}) {
                const idx = counter++;
                const html = getQuestionTemplate(idx, prefill);
                $list.append(html);

                const $card = $list.children('.question-item').last();
                const typeVal = $card.find('.field-type').val();

                // Kalau tipe-nya pakai opsi, siapkan options-box
                if (typeVal === 'checkbox' || typeVal === 'option') {
                    const $box = $card.find('.options-box');
                    const $wrap = $card.find('.options-wrapper');
                    $box.show();
                    $wrap.empty();

                    if (Array.isArray(prefill.options) && prefill.options.length) {
                        // Pakai opsi hasil clone
                        prefill.options.forEach(function(opt) {
                            addOptionRow($card, opt.label, opt.point);
                        });
                    } else {
                        // Default opsi baru
                        addOptionRow($card, 'Opsi 1', 0);
                        addOptionRow($card, 'Opsi 2', 0);
                    }
                    initOptionsSortable($card);
                    renumberOptionFields($card);
                } else {
                    $card.find('.options-box').hide();
                }

                // Supaya perubahan tipe nanti tetap jalan
                toggleOptionsBox($card);
                refreshSequencesAndNumbers();
            }

            // --- FUNGSI: TEMPLATE PERTANYAAN (UPDATED: tambah tombol CLONE) ---
            function getQuestionTemplate(idx, prefill = {}) {
                const q = prefill.question || '';
                const type = prefill.type || '';
                const requiredChecked = prefill.is_required ? 'checked' : '';
                const formId = @json($form->id);

                return `
      <div class="question-item card shadow-sm" data-index="${idx}">
        <div class="card-header d-flex align-items-center gap-2 py-2">
          <span class="drag-handle" title="Seret untuk mengurutkan">☰</span>
          <strong class="me-auto">Pertanyaan <span class="q-number"></span></strong>
          <button class="btn btn-sm btn-outline-secondary btn-clone-question" type="button">
            Clone
          </button>
          <button class="btn btn-sm btn-outline-danger btn-remove-question" type="button">
            Hapus
          </button>
        </div>
        <div class="card-body pt-3">
          <input type="hidden" name="questions[${idx}][m_form_id]" value="${formId}">
          <input type="hidden" class="field-sequence" name="questions[${idx}][sequence]" value="0">
          <div class="row g-4">
            <div class="col-md-7">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control field-question"
                       name="questions[${idx}][question]"
                       placeholder="Tulis pertanyaan"
                       value="${escapeHtml(q)}" required>
                <label>Pertanyaan</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating form-floating-outline">
                <select class="form-select field-type" name="questions[${idx}][type]" required>
                  <option value="" ${type === '' ? 'selected' : ''} disabled>-- Pilih Tipe --</option>
                  <option value="text" ${type === 'text' ? 'selected' : ''}>Text</option>
                  <option value="checkbox" ${type === 'checkbox' ? 'selected' : ''}>Checkbox (multi)</option>
                  <option value="option" ${type === 'option' ? 'selected' : ''}>Option (single)</option>
                </select>
                <label>Tipe</label>
              </div>
            </div>
            <div class="col-md-2 d-flex align-items-center">
              <div class="form-check form-switch">
                <input class="form-check-input field-required" type="checkbox"
                       name="questions[${idx}][is_required]" value="1" ${requiredChecked}>
                <label class="form-check-label">Wajib</label>
              </div>
            </div>
          </div>

          <div class="mt-4 options-box" style="display:none">
            <div class="d-flex align-items-center mb-2">
              <h6 class="mb-0 me-auto">Pilihan Jawaban</h6>
              <button type="button" class="btn btn-sm btn-outline-primary btn-add-option">
                + Tambah Opsi
              </button>
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
      </div>`;
            }

            // --- FUNGSI: CLONE PERTANYAAN + OPSI ---
            function cloneQuestion($card) {
                const typeVal = $card.find('.field-type').val();

                const prefill = {
                    question: $card.find('.field-question').val(),
                    type: typeVal,
                    is_required: $card.find('.field-required').is(':checked'),
                    options: []
                };

                if (typeVal === 'checkbox' || typeVal === 'option') {
                    $card.find('.options-wrapper .option-row').each(function() {
                        const $row = $(this);
                        const label = $row.find('input[type="text"]').val();
                        const point = $row.find('input[type="number"]').val();
                        prefill.options.push({
                            label: label,
                            point: point
                        });
                    });
                }

                // Tambah pertanyaan baru dengan prefill di atas
                addQuestion(prefill);
            }

            // --- OPSI PERTANYAAN ---
            function addOptionRow($card, defaultLabel = '', defaultPoint = 0) {
                const idx = Number($card.attr('data-index'));
                const $wrap = $card.find('.options-wrapper');
                const optionIdx = $wrap.children('.option-row').length;

                const row = `
      <div class="option-row list-group-item d-flex align-items-center gap-2">
        <span class="opt-drag" title="Seret">⋮⋮</span>
        <div class="opt-seq small text-muted">1</div>
        <input type="hidden" class="field-opt-sequence"
               name="questions[${idx}][options][${optionIdx}][sequence]"
               value="${optionIdx + 1}">
        <div class="flex-grow-1">
          <input type="text" class="form-control"
                 name="questions[${idx}][options][${optionIdx}][label]"
                 placeholder="Teks opsi" value="${escapeHtml(defaultLabel)}" required>
        </div>
        <div class="col-2">
          <input type="number" class="form-control"
                 name="questions[${idx}][options][${optionIdx}][point]"
                 placeholder="0" value="${Number(defaultPoint) || 0}"
                 step="1" min="0">
        </div>
        <button class="btn btn-outline-secondary remove-option" type="button"
                aria-label="Hapus opsi">&times;</button>
      </div>
    `;
                $wrap.append(row);
            }

            // Aktifkan Sortable per "options-wrapper"
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
                    $row.find('.field-opt-sequence')
                        .attr('name', `questions[${idx}][options][${i}][sequence]`)
                        .val(i + 1);
                    $row.find('input[type="text"]')
                        .attr('name', `questions[${idx}][options][${i}][label]`);
                    $row.find('input[type="number"]')
                        .attr('name', `questions[${idx}][options][${i}][point]`);
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
            <li class="breadcrumb-item active">Tambah Pertanyaan</li>
        </ol>
    </nav>

    <form id="form-create-questions" action="{{ route('form.question.store', $form->id) }}" method="POST">
        @csrf

        <div class="mb-3 d-flex gap-2">
            <button class="btn btn-primary" id="btn-add-question" type="button">+ Tambah Pertanyaan</button>
            <a href="{{ route('form.question.index', $form->id) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>

        <div id="question-list" class="d-grid gap-3"></div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan Semua</button>
        </div>
    </form>
@endsection
