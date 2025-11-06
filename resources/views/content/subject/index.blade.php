@extends('layouts/contentNavbarLayout')

@section('title', 'Rapor Evaluasi Mata Kuliah')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Rapor Evaluasi Mata Kuliah</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Data</h5>
        </div>
        <div class="card-body pt-0">
            <div class="row mt-1 g-5">
                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <select name="major_id" id="major_id" class="form-select @error('major_id') is-invalid @enderror"
                            autocomplete="off">
                            <option value="">Pilih Jurusan</option>
                            @foreach ($majorOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <select id="study_program_id" class="form-select @error('study_program_id') is-invalid @enderror"
                            disabled name="study_program_id">
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
                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <select id="session_id" class="form-select @error('session_id') is-invalid @enderror" disabled
                            name="session_id">
                            <option value="" selected>-- Pilih Tahun Ajaran --</option>
                        </select>
                        <label for="session_id">Tahun Ajaran</label>
                        @error('session_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <select id="semester" class="form-select @error('semester') is-invalid @enderror" disabled
                            name="semester">
                            <option value="" selected>-- Pilih Semester --</option>
                            <option value="1">Ganjil</option>
                            <option value="2">Genap</option>
                        </select>
                        <label for="semester">Semester</label>
                        @error('semester')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="row g-2 align-items-center my-3 mx-4">
            <div class="col-12 col-md">
                <div class="row g-2 justify-content-md-end">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="input-group input-group-sm">
                            <input type="search" class="form-control" placeholder="Cari berdasarkan nama" id="search"
                                name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover" id="table-subject-evaluation">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>Jumlah Responden</th>
                            <th>Rata - rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Silakan pilih filter di atas untuk menampilkan data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- paginate navigation --}}
            <div id="pagination-nav"></div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const $major = $('#major_id');
            const $prodi = $('#study_program_id');
            const $session = $('#session_id');
            const $semester = $('#semester');
            const $search = $('#search');
            const $tbody = $('#table-subject-evaluation tbody');
            const $pager = $('#pagination-nav');
            const DEFAULT_PER_PAGE = 10;

            // ---------- utils
            function setDisabled($el, disabled) {
                $el.prop('disabled', !!disabled);
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

            function populateSelect($select, items, placeholder) {
                const opts = [`<option value="">${placeholder}</option>`]
                    .concat((items || []).map(it =>
                        `<option value="${it.value}">${escapeHtml(it.label)}</option>`));
                $select.html(opts.join(''));
            }

            // ---------- resets
            function resetProdi(clear = true) {
                setDisabled($prodi, true);
                if (clear) $prodi.html('<option value="">-- Pilih Program Studi --</option>');
            }

            function resetSession(clear = true) {
                setDisabled($session, true);
                if (clear) $session.html('<option value="">-- Pilih Tahun Ajaran --</option>');
            }

            function resetSemester(clear = true) {
                setDisabled($semester, true);
                if (clear) $semester.html(
                    '<option value="">-- Pilih Semester --</option>' +
                    '<option value="1">Ganjil</option><option value="2">Genap</option>'
                );
            }

            function resetTable() {
                $tbody.html(
                    '<tr><td colspan="5" class="text-center">Silakan pilih filter di atas untuk menampilkan data.</td></tr>'
                );
                $pager.html('');
            }

            // ---------- API calls
            function getReportSubject(params) {
                return $.ajax({
                    url: "{{ route('subject.evaluation.report') }}",
                    method: 'GET',
                    dataType: 'json',
                    data: params
                });
            }

            function fetchStudyPrograms(majorId) {
                return $.ajax({
                    url: "{{ route('api.study_program.option') }}",
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        major_id: majorId
                    }
                });
            }

            function fetchSessions() {
                return $.ajax({
                    url: "{{ route('api.session.option') }}",
                    method: 'GET',
                    dataType: 'json'
                });
            }

            // ---------- renderers
            function renderRows(items, page, perPage) {
                if (!items || items.length === 0) {
                    $tbody.html(
                        '<tr><td colspan="5" class="text-center">Tidak ada data rapor evaluasi mata kuliah.</td></tr>'
                    );
                    return;
                }
                // nomor urut global
                const startNo = (Number(page || 1) - 1) * Number(perPage || DEFAULT_PER_PAGE);
                const rows = items.map((it, idx) => `
                  <tr>
                    <td>${startNo + idx + 1}</td>
                    <td>${escapeHtml(it.code)}</td>
                    <td>${escapeHtml(it.name)}</td>
                    <td>${escapeHtml(it.respondent_total)}</td>
                    <td>${escapeHtml(it.avg_score)}</td>
                  </tr>
                `);
                $tbody.html(rows.join(''));
            }

            function renderPagination(html) {
                $pager.html(html || '');
            }

            // ---------- params & load
            function currentParams(extra = {}) {
                return Object.assign({
                    major_id: $major.val(),
                    study_program_id: $prodi.val(),
                    session_id: $session.val(),
                    semester: $semester.val(),
                    per_page: DEFAULT_PER_PAGE,
                    search: $search.val()
                }, extra);
            }

            async function loadPage(page = 1) {
                const params = currentParams({
                    page
                });
                // loading state ringan
                $tbody.html('<tr><td colspan="5" class="text-center">Memuat...</td></tr>');
                try {
                    const res = await getReportSubject(params);
                    const results = res?.data?.results || [];
                    const meta = res?.meta || {};
                    renderRows(results, meta.current_page, meta.per_page);
                    renderPagination(meta.links);
                } catch (e) {
                    console.error(e);
                    Swal.fire('Gagal', e?.responseJSON?.message ||
                        'Gagal memuat data rapor evaluasi mata kuliah', 'error');
                    resetTable();
                }
            }

            // ---------- event handlers (filters)
            $major.on('change', async function() {
                const majorId = $(this).val();
                resetProdi();
                resetSession();
                resetSemester();
                resetTable();
                if (!majorId) return;
                try {
                    const res = await fetchStudyPrograms(majorId);
                    populateSelect($prodi, res?.data || [], '-- Pilih Program Studi --');
                    setDisabled($prodi, false);
                } catch (e) {
                    console.error(e);
                    Swal.fire('Gagal', e?.responseJSON?.message || 'Gagal memuat list program studi',
                        'error');
                }
            });

            $prodi.on('change', async function() {
                resetSession();
                resetSemester();
                resetTable();
                try {
                    const res = await fetchSessions();
                    populateSelect($session, res?.data || [], '-- Pilih Tahun Ajaran --');
                    setDisabled($session, false);
                } catch (e) {
                    console.error(e);
                    Swal.fire('Gagal', e?.responseJSON?.message || 'Gagal memuat list tahun ajaran',
                        'error');
                }
            });

            $session.on('change', function() {
                const sessionId = $(this).val();
                resetSemester();
                resetTable();
                if (!sessionId) return;
                setDisabled($semester, false);
            });

            $semester.on('change', function() {
                const majorId = $major.val();
                const studyProgramId = $prodi.val();
                const sessionId = $session.val();
                const semester = $(this).val();
                if (!majorId || !studyProgramId || !sessionId || !semester) return;
                loadPage(1); // mulai dari page 1
            });

            // search with debounce and brings all filters back to page 1
            let searchTimeout = null;
            $search.on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadPage(1); // mulai dari page 1
                }, 500);
            });

            // ---------- intercept pagination clicks (delegation)
            $(document).on('click', '#pagination-nav .pagination a', function(e) {
                e.preventDefault();
                const href = $(this).attr('href') || '';
                const url = new URL(href, window.location.origin);
                const page = url.searchParams.get('page') || 1;
                loadPage(parseInt(page, 10));
            });

            // init
            resetProdi();
            resetSession();
            resetSemester();
            resetTable();
        });
    </script>

@endsection
