<div class="col-md-6">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Rata-rata Skor per Prodi</h5>
                <small class="text-muted">
                    {{ request('major_id') ? 'Semua prodi di jurusan terpilih' : 'Top 7 prodi tertinggi' }}
                </small>
            </div>
            <div id="prodiScoreChart"></div>
        </div>
    </div>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const labels = @json($prodiChart['labels'] ?? []);
            const data = @json($prodiChart['data'] ?? []);
            const unit = @json($prodiChart['unit'] ?? '1-5');

            if (!document.querySelector('#prodiScoreChart')) return;

            const options2 = {
                chart: {
                    type: 'bar',
                    height: Math.max(320, 40 + (labels.length *
                        32)), // tinggi dinamis biar label tidak kepotong
                    toolbar: {
                        show: true
                    }
                },
                series: [{
                    name: 'Skor Rata-rata',
                    data: data
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
                    formatter: (val) => (val ?? 0).toFixed(2),
                    offsetX: 0,
                    style: {
                        colors: ['#fff'],
                        fontSize: '12px',
                        fontWeight: '600'
                    }
                },
                xaxis: {
                    categories: labels,
                    title: {
                        text: unit === '1-5' ? 'Skor (1–5)' : 'Nilai'
                    },
                    min: 0,
                    max: 5, // karena skala 1–5
                    tickAmount: 5
                },
                yaxis: {
                    labels: {
                        show: true
                    }
                },
                tooltip: {
                    y: {
                        formatter: (val) => (val ?? 0).toFixed(2)
                    }
                },
                grid: {
                    strokeDashArray: 3
                },
                legend: {
                    show: false
                }
            };

            new ApexCharts(document.querySelector("#prodiScoreChart"), options2).render();
        });
    </script>
@endpush
