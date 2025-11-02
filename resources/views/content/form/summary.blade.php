@extends('layouts/contentNavbarLayout')

@php
    use App\Enums\FormTypeEnum;
@endphp

@section('title', 'Ringkasan Form')

@section('vendor-style')
    <style>
        .kpi-card .value {
            font-size: 28px;
            font-weight: 700
        }

        .kpi-card .label {
            font-size: 12px;
            color: var(--bs-secondary-color)
        }

        .chart-card {
            min-height: 280px
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .25rem .5rem;
            border-radius: 999px;
            border: 1px solid var(--bs-border-color);
            background: var(--bs-body-bg)
        }

        .q-title {
            font-weight: 600
        }

        .question-card .card-header {
            background: #fafafa
        }

        .answer-snippet {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%
        }
    </style>
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.index') }}">Management Form</a></li>
            <li class="breadcrumb-item"><a href="{{ route('form.show', $form->id) }}">Detail Form</a></li>
            <li class="breadcrumb-item active">Summary</li>
        </ol>
    </nav>

    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">{{ $form->title ?? 'Tanpa Judul' }}</h4>
            <div class="text-muted">{{ $form->description }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('form.show', $form->id) }}" class="btn btn-outline-secondary">Kembali</a>
            @if ($form->type === FormTypeEnum::GENERAL->value)
                <a href="{{ route('form.summary.export', $form->id) }}?export=1" class="btn btn-primary">
                    Export Excel
                </a>
            @endif
        </div>
    </div>

    {{-- Filters sederhana (opsional) --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" id="filter_from" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" id="filter_to" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kata kunci (nama/NIM/NIP)</label>
                    <input type="text" id="filter_q" class="form-control form-control-sm"
                        placeholder="Cari responden...">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button id="btn-apply-filter" class="btn btn-primary flex-grow-1">Terapkan</button>
                    <button id="btn-reset-filter" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="label">Total Responden</div>
                    <div class="value" id="kpi_total">-</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="label">Response Rate</div>
                    <div class="value"><span id="kpi_rate">-</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="label">Terakhir Diterima</div>
                    <div class="value" id="kpi_last_at">-</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Tabel Responden --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Daftar Responden</h5>
                    <div class="text-muted small">Klik nama untuk melihat jawaban</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="respondent_table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nama</th>
                                    {{-- <th>Role</th> --}}
                                    {{-- <th>Prodi</th> --}}
                                    <th>Status</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody><!-- via JS --></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart per pertanyaan --}}
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Ringkasan Per Pertanyaan</h5>
                <div class="text-muted small">Chart otomatis berdasarkan tipe</div>
            </div>

            @foreach ($questions as $q)
                @php $qid = 'qchart_'.$q->id; @endphp
                <div class="question-card card chart-card mb-3" data-question-id="{{ $q->id }}"
                    data-type="{{ $q->type }}">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="q-title">{{ $q->sequence }}. {{ $q->question }}</div>
                        <div class="small text-muted">
                            {{ $q->type === 'text' ? 'Jawaban singkat' : ($q->type === 'checkbox' ? 'Checkbox (multi)' : 'Option (single)') }}
                        </div>
                    </div>
                    <div class="card-body">
                        @if (in_array($q->type, ['checkbox', 'option']))
                            <div id="{{ $qid }}" height="120"></div>
                            <div class="small text-muted mt-2" id="{{ $qid }}_legend"></div>
                        @else
                            {{-- Text: tampilkan contoh jawaban & tombol lihat semua --}}
                            <div id="{{ $qid }}_text_examples" class="vstack gap-2"></div>
                            <button class="btn btn-sm btn-outline-primary mt-2 btn-see-all-text"
                                data-question-id="{{ $q->id }}">
                                Lihat semua jawaban
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal: Detail Jawaban Responden --}}
    <div class="modal fade" id="respondentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Jawaban Responden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="resp_profile" class="mb-3"></div>
                    <div id="resp_answers" class="vstack gap-3"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Semua Jawaban Text --}}
    <div class="modal fade" id="allTextModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Semua Jawaban (Teks)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="all_text_list" class="vstack gap-2"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========= KONFIG API (ganti sesuai rute kamu) =========
            const FORM_ID = @json($form->id);
            const API = {
                kpi: '{{ route('form.summary.kpi', $form->id) }}',
                respondents: '{{ route('form.summary.respondents', $form->id) }}',
                answers: id =>
                    `/form/${FORM_ID}/summary/respondents/${id}`, // GET -> { profile:{...}, answers:[{q, type, value|options:[]}] }
                qStats: `/form/${FORM_ID}/summary/question-stats`, // GET -> { [question_id]: { labels:[], counts:[], percents:[], points:[] } } for checkbox/option
                qText: qid =>
                    `/form/${FORM_ID}/summary/question/${qid}/texts`, // GET -> { examples:[{text}], all:[{text}], total }
            };

            // ========= ELEMENTS =========
            const $kpiTotal = $('#kpi_total'),
                $kpiRate = $('#kpi_rate'),
                $kpiLast = $('#kpi_last_at'),
                $kpiRange = $('#kpi_range');
            const $from = $('#filter_from'),
                $to = $('#filter_to'),
                $q = $('#filter_q');
            $('#btn-apply-filter').on('click', () => {
                loadKPIs();
                loadRespondents(true);
                loadCharts();
            });
            $('#btn-reset-filter').on('click', () => {
                $from.val('');
                $to.val('');
                $q.val('');
                loadKPIs();
                loadRespondents(true);
                loadCharts();
            });

            function params() {
                return {
                    from: $from.val() || '',
                    to: $to.val() || '',
                    q: $q.val() || ''
                };
            }

            // ========= KPI =========
            async function loadKPIs() {
                try {
                    const res = await $.get(API.kpi, params());
                    $kpiTotal.text(res?.total ?? 0);
                    $kpiRate.text(res?.rate != null ? res.rate + '%' : '-');
                    $kpiLast.text(res?.last_at ?? '-');
                    $kpiRange.text(res?.range ?? '-');
                } catch (e) {
                    console.error(e);
                }
            }

            // ========= TABEL RESPONDEN (manual, tanpa DataTable) =========
            const tableBody = document.querySelector('#respondent_table tbody');
            // simple client-side pagination
            const pager = {
                page: 1,
                perPage: 10,
                rows: [],
                totalPages() {
                    return Math.max(1, Math.ceil(this.rows.length / this.perPage));
                },
                slice() {
                    const s = (this.page - 1) * this.perPage;
                    return this.rows.slice(s, s + this.perPage);
                }
            };

            function renderRespondentTable() {
                tableBody.innerHTML = '';
                const rows = pager.slice();
                if (!rows.length) {
                    tableBody.innerHTML =
                        `<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>`;
                    renderPagerControls();
                    return;
                }
                rows.forEach(r => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
        <td>${esc(r.submitted_at || '-')}</td>
        <td>${esc(r.name || '-')}</td>
        <td>${esc(r.status || '-')}</td>
        <td><button class="btn btn-sm btn-outline-primary btn-view" data-id="${r.id}">Lihat</button></td>
      `;
                    tableBody.appendChild(tr);
                });
                renderPagerControls();
            }

            // render pager controls (inject di bawah table)
            function renderPagerControls() {
                let host = document.getElementById('respondent-pager');
                if (!host) {
                    host = document.createElement('div');
                    host.id = 'respondent-pager';
                    host.className = 'd-flex justify-content-between align-items-center mt-2';
                    document.querySelector('#respondent_table').parentElement.appendChild(host);
                }
                const total = pager.totalPages();
                host.innerHTML = `
      <div class="text-muted small">Halaman ${pager.page} dari ${total} • Total ${pager.rows.length} responden</div>
      <div class="btn-group">
        <button class="btn btn-sm btn-outline-secondary" id="pg-first" ${pager.page<=1?'disabled':''}>«</button>
        <button class="btn btn-sm btn-outline-secondary" id="pg-prev" ${pager.page<=1?'disabled':''}>‹</button>
        <button class="btn btn-sm btn-outline-secondary" id="pg-next" ${pager.page>=total?'disabled':''}>›</button>
        <button class="btn btn-sm btn-outline-secondary" id="pg-last" ${pager.page>=total?'disabled':''}>»</button>
      </div>
    `;
                host.querySelector('#pg-first')?.addEventListener('click', () => {
                    pager.page = 1;
                    renderRespondentTable();
                });
                host.querySelector('#pg-prev')?.addEventListener('click', () => {
                    pager.page = Math.max(1, pager.page - 1);
                    renderRespondentTable();
                });
                host.querySelector('#pg-next')?.addEventListener('click', () => {
                    pager.page = Math.min(total, pager.page + 1);
                    renderRespondentTable();
                });
                host.querySelector('#pg-last')?.addEventListener('click', () => {
                    pager.page = total;
                    renderRespondentTable();
                });
            }

            async function loadRespondents(resetPage = false) {
                try {
                    const res = await $.get(API.respondents, params());
                    pager.rows = res?.data || [];
                    if (resetPage) pager.page = 1;
                    renderRespondentTable();
                } catch (e) {
                    console.error(e);
                }
            }

            // delegation for detail
            $('#respondent_table').on('click', '.btn-view', async function() {
                const id = $(this).data('id');
                await openRespondentDetail(id);
            });

            async function openRespondentDetail(id) {
                try {
                    const res = await $.get(API.answers(id), params());
                    const prof = res?.profile || {};
                    const answers = res?.answers || [];

                    $('#resp_profile').html(`
        <div class="d-flex flex-wrap gap-2">
          <span class="chip"><strong>${esc(prof.name || 'Tanpa Nama')}</strong></span>
          ${prof.role ? `<span class="chip">${esc(prof.role)}</span>`:''}
          ${prof.study_program ? `<span class="chip">${esc(prof.study_program)}</span>`:''}
          ${prof.submitted_at ? `<span class="chip">Waktu: ${esc(prof.submitted_at)}</span>`:''}
        </div>
      `);

                    const $list = $('#resp_answers');
                    $list.empty();
                    answers.forEach((a, i) => {
                        if (a.type === 'text') {
                            $list.append(`
            <div class="border rounded p-2">
              <div class="fw-semibold mb-1">${i+1}. ${esc(a.q)}</div>
              <div class="answer-snippet">${esc(a.value ?? '-')}</div>
            </div>
          `);
                        } else {
                            const items = (a.options || []).map(o =>
                                `<li>${esc(o.answer)} ${o.point != null ? `<span class="text-muted">(${o.point})</span>`:''}</li>`
                            ).join('');
                            $list.append(`
            <div class="border rounded p-2">
              <div class="fw-semibold mb-1">${i+1}. ${esc(a.q)}</div>
              <ul class="mb-0">${items || '<li class="text-muted">-</li>'}</ul>
            </div>
          `);
                        }
                    });
                    new bootstrap.Modal('#respondentModal').show();
                } catch (e) {
                    console.error(e);
                }
            }

            // ========= CHARTS PER QUESTION (ApexCharts) =========
            const apexRefs = {}; // canvasId -> ApexCharts instance

            async function loadCharts() {
                let stats = {};
                try {
                    stats = await $.get(API.qStats, params());
                } catch (e) {
                    console.error(e);
                }

                $('.question-card').each(function() {
                    const qid = $(this).data('question-id');
                    const type = $(this).data('type');

                    if (type === 'text') {
                        loadTextExamples(qid, $(`#qchart_${qid}_text_examples`));
                    } else {
                        const elId = `qchart_${qid}`;
                        renderChoiceChart(elId, stats?.[qid] || null);
                    }
                });
            }

            async function loadTextExamples(qid, $container) {
                try {
                    const res = await $.get(API.qText(qid), params()); // {examples,total}
                    $container.empty();
                    const ex = res?.examples || [];
                    if (!ex.length) {
                        $container.append('<div class="text-muted">Belum ada jawaban.</div>');
                        return;
                    }
                    ex.slice(0, 5).forEach(e => {
                        $container.append(
                            `<div class="border rounded p-2 answer-snippet">${esc(e.text || '')}</div>`
                        );
                    });

                    $(`.btn-see-all-text[data-question-id="${qid}"]`).off('click').on('click',
                        async function() {
                            const allRes = await $.get(API.qText(qid), {
                                ...params(),
                                all: 1
                            });
                            const all = allRes?.all || [];
                            const $list = $('#all_text_list');
                            $list.empty();
                            if (!all.length) {
                                $list.html('<div class="text-muted">Tidak ada jawaban.</div>');
                            } else {
                                all.forEach((a, i) => $list.append(
                                    `<div class="border rounded p-2"><span class="text-muted">${i+1}.</span> ${esc(a.text || '')}</div>`
                                ));
                            }
                            new bootstrap.Modal('#allTextModal').show();
                        });
                } catch (e) {
                    console.error(e);
                }
            }

            function jtBrandPalette(n) {
                const base = [
                    '#5766da', '#6f7cf0', '#4cb8ff', '#22d3ee', '#34d399', '#a3e635',
                    '#fbbf24', '#f59e0b', '#ef4444', '#ec4899', '#a78bfa', '#8b5cf6'
                ];
                return base.slice(0, Math.max(1, Math.min(n, base.length)));
            }

            // ===== Render chart pilihan (DONUT jika opsi <= 6 agar label tidak numpuk, else BAR) =====
            function renderChoiceChart(elId, s) {
                const el = document.getElementById(elId);
                if (!el) return;

                // destroy existing
                if (!window.apexRefs) window.apexRefs = {};
                if (window.apexRefs[elId]) {
                    window.apexRefs[elId].destroy();
                    delete window.apexRefs[elId];
                }

                const labels = s?.labels || [];
                const counts = s?.counts || [];
                const total = counts.reduce((a, b) => a + (Number(b) || 0), 0);

                if (!labels.length) {
                    el.innerHTML = '<div class="text-muted">Tidak ada data</div>';
                    return;
                }

                const isDonut = labels.length <= 6; // ganti ke DONUT untuk keterbacaan
                const colors = jtBrandPalette(labels.length);

                const primary = '#5766da';
                const primaryLight = '#6f7cf0';
                const fore = '#4b5563';
                const axis = '#cbd5e1';
                const grid = '#e2e8f0';

                const options = isDonut ? {
                    chart: {
                        type: 'donut',
                        height: 260,
                        foreColor: fore,
                        fontFamily: 'Inter, sans-serif'
                    },
                    series: counts,
                    labels: labels,
                    colors: colors,

                    // Garis pemisah slice tebal agar kontras
                    stroke: {
                        colors: ['#fff'],
                        width: 6
                    },

                    // Matikan label di dalam slice (biar tidak berantakan)
                    dataLabels: {
                        enabled: false
                    },

                    // Tampilkan nilai di tengah donut (nama slice aktif, value, dan total)
                    plotOptions: {
                        pie: {
                            expandOnClick: false,
                            donut: {
                                size: '68%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '12px',
                                        fontWeight: 600,
                                        offsetY: 10,
                                        color: '#334155'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '18px',
                                        fontWeight: 700,
                                        color: '#111827',
                                        formatter: (val) => String(val ?? 0)
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        color: '#334155',
                                        fontWeight: 600,
                                        formatter: () => String(total)
                                    }
                                }
                            }
                        }
                    },

                    legend: {
                        position: 'bottom',
                        labels: {
                            colors: '#111827'
                        },
                        markers: {
                            radius: 12
                        }
                    },

                    tooltip: {
                        theme: 'light',
                        fillSeriesColor: false,
                        y: {
                            formatter: (val) => {
                                const c = Number(val) || 0;
                                const pct = total ? (c * 100 / total).toFixed(1) : 0;
                                return `${c} (${pct}%)`;
                            }
                        }
                    }
                } : {
                    chart: {
                        type: 'bar',
                        height: 280,
                        foreColor: fore,
                        fontFamily: 'Inter, sans-serif'
                    },
                    series: [{
                        name: 'Jumlah',
                        data: counts
                    }],
                    colors: colors,
                    xaxis: {
                        categories: labels,
                        axisTicks: {
                            color: grid
                        },
                        axisBorder: {
                            color: axis
                        },
                        labels: {
                            rotate: -15,
                            trim: true,
                            style: {
                                colors: '#475569',
                                fontSize: '12px'
                            }
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '45%',
                            distributed: true // tiap bar warna berbeda dari palet
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            gradientToColors: labels.map(() => primaryLight),
                            opacityFrom: 0.95,
                            opacityTo: 0.9,
                            stops: [0, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    grid: {
                        borderColor: grid,
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: (val) => {
                                const c = Number(val) || 0;
                                const pct = total ? (c * 100 / total).toFixed(1) : 0;
                                return `${c} (${pct}%)`;
                            }
                        }
                    },
                    noData: {
                        text: 'Tidak ada data'
                    },
                    legend: {
                        show: false
                    }
                };

                const chart = new ApexCharts(el, options);
                chart.render();
                window.apexRefs[elId] = chart;

                // Legend ringkas di luar chart (pakai elemen *_legend jika ada)
                const legendEl = document.getElementById(`${elId}_legend`);
                if (legendEl) {
                    const html = labels.map((l, i) => {
                        const c = counts[i] ?? 0;
                        const pct = total ? (c * 100 / total).toFixed(1) : 0;
                        const swatch =
                            `<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:${colors[i % colors.length]};margin-right:6px;vertical-align:middle;"></span>`;
                        // gunakan window.esc jika ada, untuk sanitasi
                        const safeLabel = (window.esc ? esc(l) : l);
                        return `<span class="me-3">${swatch}<strong>${safeLabel}</strong>: ${c} (${pct}%)</span>`;
                    }).join('');
                    legendEl.innerHTML = html || '<span class="text-muted">Tidak ada data</span>';
                }
            }

            function esc(s) {
                if (s == null) return '';
                return String(s).replace(/[&<>\"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                } [m]));
            }

            // INIT
            loadKPIs();
            loadRespondents(true);
            loadCharts();
        });
    </script>
@endsection
