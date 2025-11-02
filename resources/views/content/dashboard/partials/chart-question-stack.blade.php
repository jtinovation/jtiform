<div class="col-md-6">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Total Jawaban (1–5) per Pertanyaan</h5>
            </div>
            <div id="questionScoreStack"></div>
        </div>
    </div>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const categories = @json($questionStackChart['categories'] ?? []);
            const series = @json($questionStackChart['series'] ?? []);

            const el = document.querySelector('#questionScoreStack');
            if (!el) return;

            // Warna Likert (gradasi jelas dari buruk -> baik)
            const colorMap = {
                'Skor 1': '#ef4444', // merah
                'Skor 2': '#f59e0b', // oranye
                'Skor 3': '#fbbf24', // kuning
                'Skor 4': '#60a5fa', // biru lembut
                'Skor 5': '#5766da' // biru brand utama
            };

            const colors = series.map(s => colorMap[s.name] || '#94a3b8'); // fallback abu

            const options3 = {
                chart: {
                    type: 'bar',
                    height: Math.max(360, 60 + (categories.length * 30)),
                    stacked: true,
                    toolbar: {
                        show: true
                    },
                    foreColor: '#4b5563',
                    fontFamily: 'Inter, sans-serif'
                },
                series: series,
                colors: colors,
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 6,
                        columnWidth: '55%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: categories,
                    labels: {
                        show: false
                    },
                    title: {
                        text: 'Pertanyaan',
                        style: {
                            color: '#334155',
                            fontWeight: 500
                        }
                    },
                    axisTicks: {
                        color: '#e5e7eb'
                    },
                    axisBorder: {
                        color: '#cbd5e1'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Jawaban',
                        style: {
                            color: '#334155',
                            fontWeight: 500
                        }
                    },
                    forceNiceScale: true,
                    labels: {
                        style: {
                            colors: '#475569'
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: 'light',
                    style: {
                        fontSize: '13px'
                    },
                    marker: {
                        show: true
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    markers: {
                        radius: 12
                    },
                    labels: {
                        colors: '#334155'
                    }
                },
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 3
                },
                fill: {
                    opacity: 0.95
                },
                states: {
                    active: {
                        filter: {
                            type: 'none'
                        }
                    },
                    hover: {
                        filter: {
                            type: 'lighten',
                            value: 0.02
                        }
                    }
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: Math.max(360, 60 + (categories.length * 38))
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '70%'
                            }
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center'
                        }
                    }
                }]
            };

            new ApexCharts(el, options3).render();
        });
    </script>
@endpush
