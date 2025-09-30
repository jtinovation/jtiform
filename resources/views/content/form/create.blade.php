@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Form')

@php
    use App\Enums\FormTypeEnum;
    use App\Enums\FormRespondentTypeEnum;
@endphp

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('form.index') }}">Management Form</a>
            </li>
            <li class="breadcrumb-item active">Tambah Form</li>
        </ol>
    </nav>
    <div class="card mb-6">
        <form action="{{ route('form.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body pt-0">
                <div class="row mt-1 g-5">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('code') is-invalid @enderror" type="text" id="code"
                                name="code" value="{{ old('code') }}" placeholder="Masukkan Kode Form" required />
                            <label for="code">Kode Form/Questionnaire</label>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('title') is-invalid @enderror" type="text" id="title"
                                name="title" value="{{ old('title') }}" placeholder="Masukkan Judul Form/Questionnaire"
                                required />
                            <label for="title">Judul Form/Questionnaire</label>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('start_at') is-invalid @enderror" type="datetime-local"
                                id="start_at" name="start_at" value="{{ old('start_at') }}"
                                placeholder="Masukkan Tanggal Mulai" required />
                            <label for="start_at">Tanggal Mulai</label>
                            @error('start_at')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('end_at') is-invalid @enderror" type="datetime-local"
                                id="end_at" name="end_at" value="{{ old('end_at') }}"
                                placeholder="Masukkan Tanggal Berakhir" required />
                            <label for="end_at">Tanggal Berakhir</label>
                            @error('end_at')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror"
                                required>
                                <option value="" disabled selected>-- Pilih Tipe --</option>
                                <option value="{{ FormTypeEnum::GENERAL }}"
                                    {{ old('type') == FormTypeEnum::GENERAL->value ? 'selected' : '' }}>Umum</option>
                                <option value="{{ FormTypeEnum::LECTURE_EVALUATION }}"
                                    {{ old('type') == FormTypeEnum::LECTURE_EVALUATION->value ? 'selected' : '' }}>
                                    Evaluasi Dosen</option>
                            </select>
                            <label for="type">Tipe</label>
                            @error('type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('cover') is-invalid @enderror" type="file" id="cover"
                                name="cover" value="{{ old('cover') }}" placeholder="Masukkan Cover" />
                            <label for="cover">Cover</label>
                            @error('cover')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                placeholder="Masukkan Deskripsi" style="height: 100px">{{ old('description') }}</textarea>
                            <label for="description">Deskripsi</label>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <select name="responden_type" id="responden_type"
                                class="form-select @error('responden_type') is-invalid @enderror">
                                <option value="" disabled selected>-- Pilih Tipe Responden --</option>
                                <option value="{{ FormRespondentTypeEnum::ALL }}">Semua Jurusan</option>
                                <option value="{{ FormRespondentTypeEnum::MAJOR }}">Jurusan</option>
                                <option value="{{ FormRespondentTypeEnum::STUDY_PROGRAM }}">Program Studi
                                </option>
                                <option value="{{ FormRespondentTypeEnum::STUDENT }}">Spesifik Mahasiswa</option>
                                <option value="{{ FormRespondentTypeEnum::LECTURER }}">Spesifik Dosen</option>
                                <option value="{{ FormRespondentTypeEnum::EDUCATIONAL_STAFF }}">Spesifik Tenaga
                                    Pendidik</option>
                            </select>
                            <label for="responden_type">Responden</label>
                            @error('responden_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="major_id" class="form-select @error('major_id') is-invalid @enderror" disabled
                                name="major_id">
                                <option value="" selected>-- Pilih Jurusan --</option>
                            </select>
                            <label for="major_id">Jurusan</label>
                            @error('major_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="study_program_id"
                                class="form-select @error('study_program_id') is-invalid @enderror" disabled
                                name="study_program_id">
                                <option value="" selected>-- Pilih Program Studi --</option>
                            </select>
                            <label for="study_program_id">Program Studi</label>
                            @error('study_program_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Area pencarian entity (student/lecturer/staff) --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="entity_search" class="form-control"
                                placeholder="Ketik untuk mencari..." disabled>
                            <label for="entity_search" id="entity_search_label">Pencarian</label>
                        </div>
                        <div id="entity_results" class="list-group mt-2" style="max-height: 240px; overflow:auto;"></div>
                    </div>

                    <div class="col-md-6">
                        <div id="selected_entities" class="d-flex flex-wrap gap-2 p-2 border rounded-2 bg-body-tertiary"
                            style="min-height:42px;">
                            {{-- chip + hidden inputs akan muncul di sini --}}
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-6">Simpan</button>
            </div>
        </form>
    </div>
@endsection

@section('page-style')
    <style>
        .chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .25rem .5rem;
            border-radius: 999px;
            border: 1px solid var(--bs-border-color);
            background: var(--bs-body-bg);
        }

        .chip .remove-chip {
            cursor: pointer;
            border: 0;
            background: transparent;
            line-height: 1;
        }
    </style>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // =======================
            // Konfigurasi endpoint
            // =======================
            const API_ENDPOINTS = {
                majors: "{{ route('api.major.option') }}", // GET
                studyPrograms: "{{ route('api.study_program.option') }}", // GET?major_id=
                student: "{{ route('api.student.option') }}", // GET?study_program_id=&q=
                lecturer: "{{ route('api.staff.option') }}", // GET?study_program_id=&q=
                educational_staff: "{{ route('api.staff.option') }}", // GET?study_program_id=&q=
            };

            // =======================
            // Elemen
            // =======================
            const $formType = $('#type');
            const $type = $('#responden_type');
            const $major = $('#major_id');
            const $prodi = $('#study_program_id');
            const $search = $('#entity_search');
            const $searchLabel = $('#entity_search_label');
            const $results = $('#entity_results');
            const $selected = $('#selected_entities');
            // const $preview  = $('#selected_preview');

            // simpan state terpilih (id → {id,label})
            const selectedMap = new Map();

            // =======================
            // Helpers
            // =======================
            function setDisabled($el, disabled) {
                $el.prop('disabled', !!disabled);
            }

            function resetMajors(clear = true) {
                setDisabled($major, true);
                if (clear) $major.html('<option value="">-- Pilih Jurusan --</option>');
            }

            function resetProdi(clear = true) {
                setDisabled($prodi, true);
                if (clear) $prodi.html('<option value="">-- Pilih Program Studi --</option>');
            }

            function resetSearchArea() {
                setDisabled($search, true);
                $search.val('');
                $results.empty();
                // $preview.val('');
                selectedMap.clear();
                $selected.empty();
            }

            function fetchMajors() {
                return $.ajax({
                    url: API_ENDPOINTS.majors,
                    method: 'GET',
                    dataType: 'json'
                });
            }

            function fetchStudyPrograms(majorId) {
                return $.ajax({
                    url: API_ENDPOINTS.studyPrograms,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        major_id: majorId
                    }
                });
            }

            function fetchEntities(kind, majorId, studyProgramId, query) {
                const url = API_ENDPOINTS[kind];

                return $.ajax({
                    url,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        major_id: majorId,
                        study_program_id: studyProgramId,
                        q: query || '',

                    }
                });
            }

            function populateSelect($select, items, placeholder) {
                const opts = [`<option value="">${placeholder}</option>`]
                    .concat((items || []).map(it =>
                        `<option value="${it.value}">${escapeHtml(it.label)}</option>`));
                $select.html(opts.join(''));
            }

            function escapeHtml(str) {
                if (str == null) return '';
                return String(str).replace(/[&<>"']/g, s => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                } [s]));
            }

            function updateSelectedPreview() {
                // if (!$preview.length) return;
                // const arr = Array.from(selectedMap.values()).map(v => `${v.label} (${v.id})`);
                // $preview.val(arr.join('\n'));
            }

            function renderResults(items) {
                $results.empty();
                if (!items || !items.length) {
                    $results.append('<div class="list-group-item text-muted fst-italic">Tidak ada hasil</div>');
                    return;
                }
                items.forEach(it => {
                    // kalau sudah terpilih, tandai disabled
                    const exists = selectedMap.has(it.value);
                    const item = $(`
        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span>${escapeHtml(it.label)}</span>
          ${ exists ? '<span class="badge bg-secondary">Dipilih</span>' : '<span class="badge bg-primary">Pilih</span>'}
        </button>
      `);
                    item.data('item', it);
                    item.prop('disabled', exists);
                    $results.append(item);
                });
            }

            function addChip(item) {
                if (selectedMap.has(item.value)) return;
                selectedMap.set(item.value, {
                    id: item.value,
                    label: item.label
                });

                const chip = $(`
      <span class="chip">
        <span>${escapeHtml(item.label)}</span>
        <button class="remove-chip" type="button" aria-label="Remove">&times;</button>
        <input type="hidden" name="respondent_ids[]" value="${item.value}">
      </span>
    `);
                chip.find('.remove-chip').on('click', function() {
                    selectedMap.delete(item.value);
                    chip.remove();
                    updateSelectedPreview();
                    // enable lagi di hasil
                    $results.find('button.list-group-item').each(function() {
                        const it = $(this).data('item');
                        if (it && it.value === item.value) {
                            $(this).prop('disabled', false).find('.badge').removeClass(
                                'bg-secondary').addClass('bg-primary').text('Pilih');
                        }
                    });
                });

                $selected.append(chip);
                updateSelectedPreview();
            }

            function setSearchPlaceholderByType(t) {
                let label = 'Pencarian';
                if (t === 'student') label = 'Cari Mahasiswa (nim/nama)...';
                if (t === 'lecturer') label = 'Cari Dosen (nip/nama)...';
                if (t === 'educational_staff') label = 'Cari Tendik (nip/nama)...';
                $search.attr('placeholder', label);
                $searchLabel.text(label);
            }

            // =======================
            // Events
            // =======================
            $formType.on('change', function() {
                const val = $(this).val();
                if (val === '{{ FormTypeEnum::LECTURE_EVALUATION->value }}') {
                    // kalau ganti ke evaluasi dosen, set responden ke mahasiswa
                    $type.val('{{ FormRespondentTypeEnum::STUDENT->value }}').trigger('change');
                    // disable pilihan lain
                    $type.find('option').each(function() {
                        const v = $(this).val();
                        if (v !== '{{ FormRespondentTypeEnum::STUDENT->value }}') {
                            $(this).prop('disabled', true);
                        }
                    });
                } else {
                    // enable semua pilihan
                    $type.find('option').prop('disabled', false);
                }
            });

            $type.on('change', async function() {
                const val = $(this).val();

                // reset semua dulu
                resetMajors();
                resetProdi();
                resetSearchArea();

                // ALL = tidak perlu apa-apa
                if (val === 'all') {
                    // selesai
                    return;
                }

                // Major only: enable majors
                if (val === 'major') {
                    try {
                        const res = await fetchMajors();
                        populateSelect($major, res?.data || [], '-- Pilih Jurusan --');
                        setDisabled($major, false);
                    } catch (e) {
                        console.error(e);
                        alert('Gagal memuat list jurusan');
                    }
                    return;
                }

                // Study program OR entity-level -> enable majors terlebih dahulu
                if (['study_program', 'student', 'lecturer', 'educational_staff'].includes(val)) {
                    try {
                        const res = await fetchMajors();
                        populateSelect($major, res?.data || [], '-- Pilih Jurusan --');
                        setDisabled($major, false);
                    } catch (e) {
                        console.error(e);
                        alert('Gagal memuat list jurusan');
                    }

                    // set placeholder search kalau tipe entity
                    setSearchPlaceholderByType(val);
                }
            });

            // on major change → load prodi
            $major.on('change', async function() {
                const t = $type.val();
                const majorId = $(this).val();
                resetProdi();

                if (!majorId) return;

                try {
                    const res = await fetchStudyPrograms(majorId);
                    populateSelect($prodi, res?.data || [], '-- Pilih Program Studi --');
                    setDisabled($prodi, false);
                } catch (e) {
                    console.error(e);
                    alert('Gagal memuat list prodi');
                }

                // Kalau tipe = major, cukup pilih major saja (prodi tetap disabled)
                if (t === 'major') {
                    setDisabled($prodi, true);
                }
            });

            // on prodi change → untuk tipe entity enable search
            $prodi.on('change', function() {
                const t = $type.val();
                const hasProdi = !!$(this).val();

                // Study program (non-entity): cukup pilih prodi
                if (t === 'study_program') {
                    setDisabled($search, true);
                    $results.empty();
                    $selected.empty();
                    selectedMap.clear();
                    return;
                }

                if (['student', 'lecturer', 'educational_staff'].includes(t)) {
                    setDisabled($search, !hasProdi);
                    if (!hasProdi) {
                        $results.empty();
                        $selected.empty();
                        selectedMap.clear();
                    }
                }
            });

            // search typing (debounce)
            let debounceTimer = null;
            $search.on('input', function() {
                const t = $type.val();
                const spId = $prodi.val();
                const majorId = $major.val();
                const q = $(this).val();

                if (!spId || !['student', 'lecturer', 'educational_staff'].includes(t)) return;

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(async () => {
                    try {
                        const res = await fetchEntities(t, majorId, spId, q);
                        renderResults(res?.data || []);
                    } catch (e) {
                        console.error(e);
                        $results.html(
                            '<div class="list-group-item text-danger">Gagal memuat hasil</div>'
                        );
                    }
                }, 300);
            });

            // pilih hasil
            $results.on('click', '.list-group-item', function() {
                const data = $(this).data('item');
                if (!data) return;
                addChip(data);
                // Tandai sebagai dipilih
                $(this).prop('disabled', true).find('.badge').removeClass('bg-primary').addClass(
                    'bg-secondary').text('Dipilih');
            });

            // =======================
            // Boot awal: biarkan semua selain type disabled
            // =======================
            resetMajors();
            resetProdi();
            resetSearchArea();
        });
    </script>
@endsection
