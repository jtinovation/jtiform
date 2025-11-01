@extends('layouts/contentNavbarLayout')

@section('title', 'Rapor Evaluasi Prodi')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Rapor Evaluasi Prodi</li>
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
        <div class="card-header">
            <div class="card-title">
                <button type="button" class="btn btn-primary" id="btn-re-generate-data" disabled>Generate ulang</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover" id="table-evaluation">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pertanyaan</th>
                            <th>Jumlah Responden</th>
                            <th>Sangat Baik (5)</th>
                            <th>Baik (4)</th>
                            <th>Cukup (3)</th>
                            <th>Kurang (2)</th>
                            <th>Sangat Kurang (1)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Silakan pilih filter di atas untuk menampilkan data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
            const $btnGenerateData = $('#btn-generate-data');
            const $btnReGenerateData = $('#btn-re-generate-data');

            function setDisabled($el, disabled) {
                $el.prop('disabled', !!disabled);
            }

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
                if (clear) $semester.html('<option value="">-- Pilih Semester --</option>' +
                    '<option value="1">Ganjil</option>' +
                    '<option value="2">Genap</option>');
            }

            function resetReGenerateButton() {
                $btnReGenerateData.prop('disabled', true);
            }

            function resetTable() {
                $('#table-evaluation tbody').html(
                    '<tr><td colspan="8" class="text-center">Silakan pilih filter di atas untuk menampilkan data.</td></tr>'
                );
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

            function getDataReportProgram(params) {
                return $.ajax({
                    url: "{{ route('study.program.evaluation.data') }}",
                    method: 'GET',
                    dataType: 'json',
                    data: params
                });
            }

            function reGenerateReportProgram(params) {
                return $.ajax({
                    url: "{{ route('study.program.evaluation.regenerate') }}",
                    method: 'POST',
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
                    dataType: 'json',
                });
            }

            $major.on('change', async function() {
                const majorId = $(this).val();
                resetProdi();
                resetSession();
                resetSemester();
                resetTable();
                resetReGenerateButton();

                if (!majorId) return;

                try {
                    const res = await fetchStudyPrograms(majorId);
                    populateSelect($prodi, res?.data || [], '-- Pilih Program Studi --');
                    setDisabled($prodi, false);
                } catch (e) {
                    console.error(e);
                    Swal.fire(
                        'Gagal',
                        e?.responseJSON?.message || 'Gagal memuat list program studi',
                        'error'
                    );
                }
            });

            $prodi.on('change', async function() {
                resetSession();
                resetSemester();
                resetTable();
                resetReGenerateButton();

                try {
                    const res = await fetchSessions();
                    populateSelect($session, res?.data || [], '-- Pilih Tahun Ajaran --');
                    setDisabled($session, false);
                } catch (e) {
                    console.error(e);
                    Swal.fire(
                        'Gagal',
                        e?.responseJSON?.message || 'Gagal memuat list tahun ajaran',
                        'error'
                    );
                }
            });

            $session.on('change', async function() {
                const sessionId = $(this).val();
                resetSemester();
                resetTable();
                resetReGenerateButton();

                if (!sessionId) return;

                setDisabled($semester, false);
            });

            $semester.on('change', async function() {
                const majorId = $major.val();
                const studyProgramId = $prodi.val();
                const sessionId = $session.val();
                const semester = $(this).val();

                if (!majorId || !studyProgramId || !sessionId || !semester) return;

                $btnReGenerateData.prop('disabled', false);

                try {
                    $('#table-evaluation tbody').html(
                        '<tr><td colspan="8" class="text-center">Memuat data...</td></tr>'
                    );
                    const res = await getDataReportProgram({
                        major_id: majorId,
                        study_program_id: studyProgramId,
                        session_id: sessionId,
                        semester: semester
                    });
                    const results = res?.data?.results || [];

                    if (results.length === 0) {
                        $('#table-evaluation tbody').html(
                            '<tr><td colspan="8" class="text-center">Data belum tersedia.<br><button type="button" class="btn btn-primary mt-2" id="btn-generate-data">Generate Data</button></td></tr>'
                        );
                        return;
                    }

                    const rows = results.map((item, idx) => {
                        return `<tr>
                            <td>${idx + 1}</td>
                            <td>${escapeHtml(item.question)}</td>
                            <td>${item.total_respondents || 0}</td>
                            <td>${item.percentage_score_5 || 0}</td>
                            <td>${item.percentage_score_4 || 0}</td>
                            <td>${item.percentage_score_3 || 0}</td>
                            <td>${item.percentage_score_2 || 0}</td>
                            <td>${item.percentage_score_1 || 0}</td>
                        </tr>`;
                    });
                    $('#table-evaluation tbody').html(rows.join(''));
                } catch (e) {
                    Swal.fire(
                        'Gagal',
                        e?.responseJSON?.message || 'Gagal memuat data laporan',
                        'error'
                    );
                    $('#table-evaluation tbody').html(
                        '<tr><td colspan="8" class="text-center">Gagal memuat data laporan</td></tr>'
                    );
                }
            });

            $(document).on('click', '#btn-generate-data', async function() {
                const majorId = $major.val();
                const studyProgramId = $prodi.val();
                const sessionId = $session.val();
                const semester = $semester.val();

                if (!majorId || !studyProgramId || !sessionId || !semester) {
                    Swal.fire(
                        'Peringatan',
                        'Silakan pilih filter terlebih dahulu',
                        'warning'
                    );
                    return;
                }

                try {
                    $btnGenerateData.prop('disabled', true).text('Memproses...');
                    await $.ajax({
                        url: "{{ route('study.program.evaluation.generate') }}",
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            major_id: majorId,
                            study_program_id: studyProgramId,
                            session_id: sessionId,
                            semester: semester,
                            _token: '{{ csrf_token() }}'
                        }
                    });
                    Swal.fire(
                        'Berhasil',
                        'Proses generate data laporan telah dijalankan. Silakan tunggu beberapa saat dan muat ulang halaman ini.',
                        'success'
                    );

                } catch (e) {
                    console.error(e);
                    Swal.fire(
                        'Gagal',
                        e?.responseJSON?.message ||
                        'Gagal menjalankan proses generate data laporan',
                        'error'
                    );
                } finally {
                    $btnGenerateData.prop('disabled', false).text('Generate Data');
                }
            });

            $btnReGenerateData.on('click', async function() {
                const majorId = $major.val();
                const studyProgramId = $prodi.val();
                const sessionId = $session.val();
                const semester = $semester.val();

                if (!majorId || !studyProgramId || !sessionId || !semester) {
                    Swal.fire(
                        'Peringatan',
                        'Silakan pilih filter terlebih dahulu',
                        'warning'
                    );
                    return;
                }

                try {
                    $btnReGenerateData.prop('disabled', true).text('Memproses...');
                    await reGenerateReportProgram({
                        major_id: majorId,
                        study_program_id: studyProgramId,
                        session_id: sessionId,
                        semester: semester,
                        _token: '{{ csrf_token() }}'
                    });
                    Swal.fire(
                        'Berhasil',
                        'Proses generate ulang data laporan telah dijalankan. Silakan tunggu beberapa saat dan muat ulang halaman ini.',
                        'success'
                    );
                } catch (e) {
                    console.error(e);
                    Swal.fire(
                        'Gagal',
                        e?.responseJSON?.message ||
                        'Gagal menjalankan proses generate ulang data laporan',
                        'error'
                    );
                } finally {
                    $btnReGenerateData.prop('disabled', false).text('Generate ulang');
                }
            });

            resetProdi();
            resetSession();
            resetSemester();
            resetTable();
            resetReGenerateButton();

        });
    </script>
@endsection
