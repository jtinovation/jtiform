{{-- used in resources\views\content\lecture\evaluation\index.blade.php --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('lecture.evaluation.index') }}" method="GET" id="filter-form">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <label class="form-label mb-1">Prodi</label>
                    <select class="form-select" id="study_program_id" name="study_program_id" disabled
                        autocomplete="off">
                        <option value="">-- Pilih Program Studi --</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const $major = $('#major_id');
            const $prodi = $('#study_program_id');
            const $filterForm = $('#filter-form');
            const $search = $('#search');
            const BASE_URL = "{{ route('lecture.evaluation.index') }}";

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

                // Jika ada selectedProdi di URL, tampilkan dulu sementara agar "tidak hilang"
                if (selectedProdi) {
                    // Pastikan enabled & tampilkan placeholder sementara
                    setDisabled($prodi, false);

                    // Kalau option-nya belum ada (karena masih disabled di server-render),
                    // tampilkan option sementara agar user melihat nilai terpilih.
                    if ($prodi.find(`option[value="${selectedProdi}"]`).length === 0) {
                        $prodi.append(
                            `<option value="${selectedProdi}" selected>(memuat...)</option>`
                        );
                    }
                    $prodi.val(String(selectedProdi));
                } else {
                    resetProdi(); // tidak ada prodi terpilih
                }

                if (!majorId) return; // tidak ada jurusan, tidak usah fetch prodi

                try {
                    const res = await fetchStudyPrograms(majorId);
                    populateSelect($prodi, res?.data || [], '-- Pilih Program Studi --');
                    setDisabled($prodi, false);

                    // Setelah data asli datang, set kembali nilai terpilih dari URL (kalau ada)
                    if (selectedProdi) {
                        // jQuery .val() cocok untuk string; pastikan tipe string
                        $prodi.val(String(selectedProdi));

                        // Jika value tidak ada di list (mismatch tipe/ID), pertahankan option sementara
                        if ($prodi.val() !== String(selectedProdi)) {
                            $prodi.append(
                                `<option value="${selectedProdi}" selected>${selectedProdi}</option>`
                            ).val(String(selectedProdi));
                        }
                    }
                } catch (e) {
                    console.error(e);
                }
            }

            function applyAndGo(patch = {}) {
                const params = new URLSearchParams(window.location.search);

                Object.entries(patch).forEach(([k, v]) => {
                    if (v === '' || v == null) params.delete(k);
                    else params.set(k, v);
                });

                const query = params.toString();
                window.location.assign(query ? `${BASE_URL}?${query}` : BASE_URL);
            }

            // Ubah Jurusan => reset prodi lama, tapi tidak utak-atik search
            $major.on('change', function() {
                const majorVal = $major.val() || '';
                resetProdi(true);
                applyAndGo({
                    major_id: majorVal,
                    study_program_id: '' // kosongkan prodi lama agar konsisten
                });
            });

            // Ubah Prodi => preserve major & search
            $prodi.on('change', function() {
                setDisabled($prodi, false);
                applyAndGo({
                    study_program_id: $prodi.val() || ''
                });
            });

            // Debounce search: tidak menyentuh major/prodi sama sekali
            let debounceTimer = null;
            $search.on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    applyAndGo({
                        search: ($search.val() || '')
                        .trim() // kosong => param search dihapus
                    });
                }, 500);
            });

            // Initial hydrate
            hydrateProdiFromRequest();
        });
    </script>
@endpush
