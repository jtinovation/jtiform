<div class="col-12">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter</h5>

            <form method="GET" id="filterForm">
                <div class="row g-3">
                    {{-- Tahun Akademik (session_id) --}}
                    <div class="col-md-3">
                        <label class="form-label mb-1">Tahun Akademik</label>
                        <select class="form-select" name="session_id">
                            @php
                                $requestedSessionId = request('session_id');
                                $hasSelected = false;
                            @endphp
                            @foreach ($sessions as $session)
                                @php
                                    $isActive = !empty($session['is_active']);
                                    $selectThis = false;

                                    if (!$hasSelected) {
                                        if ($requestedSessionId !== null && $requestedSessionId !== '') {
                                            $selectThis = (string) $requestedSessionId === (string) $session['value'];
                                        } elseif ($isActive) {
                                            $selectThis = true;
                                        }
                                        if ($selectThis) {
                                            $hasSelected = true;
                                        }
                                    }
                                @endphp
                                <option value="{{ $session['value'] }}" {{ $selectThis ? 'selected' : '' }}>
                                    {{ $session['label'] }}@if ($isActive)
                                        — Aktif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Semester (is_even) --}}
                    <div class="col-md-3">
                        <label class="form-label mb-1">Semester</label>
                        <select class="form-select" name="is_even">
                            <option value="0" {{ request('is_even') === '0' ? 'selected' : '' }}>Ganjil</option>
                            <option value="1" {{ request('is_even') === '1' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    {{-- Jurusan (major) --}}
                    <div class="col-md-3">
                        <label class="form-label mb-1">Jurusan</label>
                        <select class="form-select" id="major_id" name="major_id" autocomplete="off">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach ($majors as $major)
                                <option value="{{ $major['value'] }}"
                                    {{ request('major_id') == $major['value'] ? 'selected' : '' }}>
                                    {{ $major['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Prodi --}}
                    <div class="col-md-3">
                        <label class="form-label mb-1">Prodi</label>
                        <select class="form-select" id="study_program_id" name="study_program_id">
                            <option value="">-- Pilih Program Studi --</option>
                            {{-- opsi akan diisi via AJAX seperti skripmu --}}
                        </select>
                    </div>

                    {{-- <div class="col-12 col-md-6 col-lg-4 col-xxl-2">
                        <label class="form-label mb-1">Jenis Form</label>
                        <select class="form-select" name="type">
                            <option value="">Semua</option>
                            <option value="lecture_evaluation"
                                {{ request('type') === 'lecture_evaluation' ? 'selected' : '' }}>Evaluasi Dosen</option>
                            <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>Kuesioner Umum
                            </option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 col-xxl-2">
                        <label class="form-label mb-1">Status Form</label>
                        <select class="form-select" name="status">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>Selesai
                            </option>
                        </select>
                    </div> --}}
                </div>
            </form>

            {{-- chips contoh (opsional) --}}
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                {{-- render chips dari request() kalau mau --}}
                <a href="{{ url()->current() }}" class="ms-1 small text-muted text-decoration-underline">Clear all</a>
            </div>
        </div>
    </div>
</div>

@push('page-script')
    <script>
        // auto-submit saat filter diubah
        document.querySelectorAll('#filterForm select').forEach(el => {
            el.addEventListener('change', () => document.getElementById('filterForm').submit());
        });

        // skrip AJAX prodi milikmu (disalin, plus isi nilai terpilih)
        document.addEventListener("DOMContentLoaded", function() {
            const $major = $('#major_id');
            const $prodi = $('#study_program_id');

            function setDisabled($el, disabled) {
                $el.prop('disabled', !!disabled);
            }

            function resetProdi(clear = true) {
                setDisabled($prodi, true);
                if (clear) $prodi.html('<option value="">-- Pilih Program Studi --</option>');
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

            async function hydrateProdiFromRequest() {
                const majorId = $major.val();
                const selectedProdi = @json(request('study_program_id'));
                resetProdi();
                if (!majorId) return;
                try {
                    const res = await fetchStudyPrograms(majorId);
                    populateSelect($prodi, res?.data || [], '-- Pilih Program Studi --');
                    setDisabled($prodi, false);
                    if (selectedProdi) $prodi.val(selectedProdi);
                } catch (e) {
                    console.error(e);
                }
            }

            $major.on('change', hydrateProdiFromRequest);
            hydrateProdiFromRequest();
        });
    </script>
@endpush
