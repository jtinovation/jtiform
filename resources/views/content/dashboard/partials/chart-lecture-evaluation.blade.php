@php
    use App\Helpers\GlobalHelper;
@endphp

<div class="col-12">
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ri ri ri-trophy-line icon-24px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ number_format($lectureReportData['kpi']['overall'] ?? 0, 2) }}
                            ({{ $lectureReportData['kpi']['predicate'] ?? '-' }})</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Nilai Keseluruhan</h6>
                    <p class="mb-0">
                        <span class="me-1 fw-medium">
                            {!! GlobalHelper::trendBadgeDashboard($lectureReportData['kpi']['trendPct'] ?? 0) !!}
                        </span>
                        <small class="text-body-secondary">vs report sebelumnya</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ri ri-book-2-line icon-24px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ number_format($lectureReportData['kpi']['courses'] ?? 0) }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Jumlah Mata Kuliah</h6>
                    <p class="mb-0">
                        <span class="me-1 fw-medium">
                            {!! GlobalHelper::trendBadgeDashboard($lectureReportData['kpi']['coursesTrendPct'] ?? 0) !!}
                        </span>
                        <small class="text-body-secondary">vs report sebelumnya</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ri ri-user-line icon-24px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ number_format($lectureReportData['kpi']['respondents'] ?? 0) }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Total Responden</h6>
                    <p class="mb-0">
                        <span class="me-1 fw-medium">
                            {!! GlobalHelper::trendBadgeDashboard($lectureReportData['kpi']['respondentsTrendPct'] ?? 0) !!}
                        </span>
                        <small class="text-body-secondary">vs report sebelumnya</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ri ri-file-text-line icon-24px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="{{ $lectureReportData['selectedReport']?->form?->code ?? '-' }}">
                            {{ \Illuminate\Support\Str::limit($lectureReportData['selectedReport']?->form?->code ?? '-', 10) }}
                        </h4>
                    </div>

                    <h6 class="mb-0 fw-normal" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ $lectureReportData['selectedReport']?->form?->title ?? '-' }}">
                        {{ \Illuminate\Support\Str::limit($lectureReportData['selectedReport']?->form?->title ?? '-', 30) }}
                    </h6>

                    <p class="mb-0">
                        Dibuat pada
                        {{ $lectureReportData['selectedReport']?->created_at?->translatedFormat('d F Y') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="card">
        <div class="card-body text-nowrap">
            <h5 class="card-title mb-0 flex-wrap text-nowrap">
                Rekapitulasi Rapor Evaluasi {{ $user->name }}
            </h5>
            <div id="reportChart"></div>
        </div>
    </div>
</div>

<div class="col-12 col-md-6">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Rata-rata Nilai per Matakuliah
                    {{ $lectureReportData['selectedReport']?->form?->code }}</h5>
            </div>
            <div id="courseBarChart"></div>
        </div>
    </div>
</div>

<div class="col-12 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Rata-rata per Pertanyaan
                    {{ $lectureReportData['selectedReport']?->form?->code }}</h5>
            </div>
            <div id="questionBarChart"></div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-2">Detail Matakuliah {{ $lectureReportData['selectedReport']?->form?->code }}</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kelas</th>
                            <th>Kode</th>
                            <th>Matakuliah</th>
                            <th class="text-end">Responden</th>
                            <th class="text-end">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lectureReportData['coursesTable'] as $r)
                            <tr>
                                <td>{{ $r['no'] }}</td>
                                <td>{{ $r['class'] }}</td>
                                <td>{{ $r['code'] }}</td>
                                <td>{{ $r['name'] }}</td>
                                <td class="text-end">{{ $r['respondents'] }}</td>
                                <td class="text-end">{{ $r['avg'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@php
    use Illuminate\Support\Facades\Route;
    $routeName = Route::currentRouteName();
@endphp
@if ($routeName !== 'dashboard' && $routeName !== 'dashboard.my')
    <div class="col-12">
        @include('content.lecture.evaluation.my.partials.table')
    </div>
@endif

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const chartLabels = @json($lectureReportData['reportChartData']['chartLabels']);
            const chartData = @json($lectureReportData['reportChartData']['chartData']);
            const chartPredicates = @json($lectureReportData['reportChartData']['chartPredicates']);

            if (!document.querySelector('#reportChart')) return;

            const options = {
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                series: [{
                    name: 'Nilai Rata-Rata',
                    data: chartData
                }],
                xaxis: {
                    categories: chartLabels,
                    title: {
                        text: 'Periode Evaluasi'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Nilai (Skala 100)'
                    },
                },
                tooltip: {
                    y: {
                        formatter: function(value, {
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            const predicate = chartPredicates[dataPointIndex];
                            return `${value} (Predikat: ${predicate})`;
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                },
            };
            const chart = new ApexCharts(document.querySelector("#reportChart"), options);
            chart.render();

            // Bar per Matakuliah
            const courseLabels = @json($lectureReportData['courseBar']['labels']);
            const courseData = @json($lectureReportData['courseBar']['data']);
            if (document.querySelector("#courseBarChart")) {
                const barOpt = {
                    chart: {
                        type: 'bar',
                        height: Math.max(320, 40 + (courseLabels.length * 28))
                    },
                    series: [{
                        name: 'Rata-rata',
                        data: courseData
                    }],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            dataLabels: {
                                position: 'center'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: (v) => (v ?? 0).toFixed(2),
                        offsetX: 0,
                        style: {
                            colors: ['#fff'],
                            fontSize: '12px',
                            fontWeight: '600'
                        }
                    },
                    xaxis: {
                        categories: courseLabels,
                        tickAmount: 5,
                        title: {
                            text: '0–100'
                        }
                    },
                    grid: {
                        strokeDashArray: 3
                    },
                    tooltip: {
                        y: {
                            formatter: (v) => (v ?? 0).toFixed(2)
                        }
                    },
                };
                new ApexCharts(document.querySelector("#courseBarChart"), barOpt).render();
            }

            // === Bar Horizontal per Pertanyaan (P1, P2, ...) ===
            const qShort = @json($lectureReportData['questionBar']['labels']); // ["P1","P2",...]
            const qFull = @json($lectureReportData['questionBar']['fullLabels']); // ["1. Pertanyaan lengkap...", ...]
            const qData = @json($lectureReportData['questionBar']['data']); // [.. 0-100 ..]

            if (document.querySelector("#questionBarChart")) {
                const qOpt = {
                    chart: {
                        type: 'bar',
                        height: Math.max(360, 40 + (qShort.length * 28)),
                        toolbar: {
                            show: true
                        }
                    },
                    series: [{
                        name: 'Rata-rata',
                        data: qData
                    }],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            borderRadius: 6,
                            dataLabels: {
                                position: 'right'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: (v) => (v ?? 0).toFixed(2)
                    },
                    xaxis: {
                        categories: qShort,
                        labels: {
                            show: true
                        }
                    },
                    yaxis: {
                        labels: {
                            show: true
                        }
                    },
                    tooltip: {
                        shared: false,
                        intersect: false,
                        x: {
                            formatter: function(val, opts) {
                                const idx = opts?.dataPointIndex ?? 0;
                                return qFull[idx] || val;
                            }
                        },
                        y: {
                            formatter: (v) => (v ?? 0).toFixed(2)
                        }
                    },
                    grid: {
                        strokeDashArray: 3
                    },
                    legend: {
                        show: false
                    }
                };
                new ApexCharts(document.querySelector("#questionBarChart"), qOpt).render();
            }
        });
    </script>
@endpush
