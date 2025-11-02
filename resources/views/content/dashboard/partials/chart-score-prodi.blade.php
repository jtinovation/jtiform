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

            const el = document.querySelector('#prodiScoreChart');
            if (!el) return;

            const primaryColor = '#5766da';
            const primaryLight = '#6f7cf0';
            const primaryGradient = `linear-gradient(90deg, ${primaryColor} 0%, ${primaryLight} 100%)`;

            const options2 = {
                chart: {
                    type: 'bar',
                    height: Math.max(320, 40 + (labels.length * 32)),
                    toolbar: {
                        show: true
                    },
                    foreColor: '#4b5563', // teks label sedikit abu, Materio style
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Skor Rata-rata',
                    data: data
                }],
                colors: [primaryColor],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6,
                        columnWidth: '65%',
                        distributed: false,
                        dataLabels: {
                            position: 'center'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => (val ?? 0).toFixed(2),
                    style: {
                        colors: ['#fff'],
                        fontSize: '12px',
                        fontWeight: '600'
                    }
                },
                xaxis: {
                    categories: labels,
                    title: {
                        text: unit === '1-5' ? 'Skor (1–5)' : 'Nilai',
                        style: {
                            color: '#334155',
                            fontWeight: 500
                        }
                    },
                    min: 0,
                    max: 5,
                    tickAmount: 5,
                    axisTicks: {
                        color: '#e5e7eb'
                    },
                    axisBorder: {
                        color: '#cbd5e1'
                    },
                    labels: {
                        style: {
                            fontSize: '13px',
                            colors: '#475569'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '13px',
                            colors: '#475569'
                        }
                    }
                },
                tooltip: {
                    theme: 'light',
                    style: {
                        fontSize: '13px'
                    },
                    y: {
                        formatter: (val) => (val ?? 0).toFixed(2)
                    },
                    marker: {
                        show: true,
                        fillColors: [primaryColor]
                    }
                },
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 3
                },
                legend: {
                    show: false
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'horizontal',
                        shadeIntensity: 0.25,
                        gradientToColors: [primaryLight],
                        inverseColors: false,
                        opacityFrom: 0.9,
                        opacityTo: 0.9,
                        stops: [0, 100]
                    }
                }
            };

            new ApexCharts(el, options2).render();
        });
    </script>
@endpush
