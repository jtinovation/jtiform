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

            if (!document.querySelector('#questionScoreStack')) return;

            const options3 = {
                chart: {
                    type: 'bar',
                    height: Math.max(360, 60 + (categories.length * 30)),
                    stacked: true,
                    toolbar: {
                        show: true
                    }
                },
                series: series,
                xaxis: {
                    categories: categories,
                    labels: {
                        show: false
                    },
                    title: {
                        text: 'Pertanyaan'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Jawaban'
                    },
                    forceNiceScale: true
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 6
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    markers: {
                        radius: 12
                    }
                },
                grid: {
                    strokeDashArray: 3
                }
            };

            new ApexCharts(document.querySelector("#questionScoreStack"), options3).render();
        });
    </script>
@endpush
