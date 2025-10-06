@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // 1. Ambil data dari PHP dan ubah menjadi format JSON yang aman untuk JS
            const chartLabels = @json($reportChartData['chartLabels']);
            const chartData = @json($reportChartData['chartData']);
            const chartPredicates = @json($reportChartData['chartPredicates']);

            // 2. Konfigurasi (options) untuk ApexCharts
            const options = {
                chart: {
                    type: 'area', // Tipe chart, bisa juga 'line', 'area', dll.
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
                // 3. Kustomisasi Tooltip untuk menampilkan Predikat
                tooltip: {
                    y: {
                        formatter: function(value, {
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            // Ambil predikat berdasarkan index data point yang sedang di-hover
                            const predicate = chartPredicates[dataPointIndex];
                            return `${value} (Predikat: ${predicate})`;
                        }
                    }
                },
                dataLabels: {
                    enabled: true, // Menampilkan nilai di atas bar
                },
            };

            // 4. Inisialisasi dan render chart
            const chart = new ApexCharts(document.querySelector("#reportChart"), options);
            chart.render();
        });
    </script>
@endsection

@section('content')
    <div class="row gy-6">
        @if ($activeForm && $activeForm->count() > 0)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-nowrap">
                        <h5 class="card-title mb-0 flex-wrap text-nowrap">
                            Ada Form Kuesioner/Evaluasi Baru yang bisa diisi! 🎉
                        </h5>
                        <p class="mb-2">Silahkan cek di menu Kuesioner/Evaluasi.</p>
                        <h4 class="text-primary mb-0">
                            {{ $activeForm->first()->title }}
                        </h4>
                        <a href="{{ route('form.fill', $activeForm->first()->id) }}"
                            class="btn btn-sm btn-primary waves-effect waves-light">Lihat
                            Form</a>
                    </div>
                    <img src="https://demos.themeselection.com/materio-bootstrap-html-laravel-admin-template-free/demo/assets/img/illustrations/trophy.png"
                        class="position-absolute bottom-0 end-0 me-5 mb-5" width="83" alt="view sales">
                </div>
            </div>
        @endif

        @role('lecturer')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-nowrap">
                        <h5 class="card-title mb-0 flex-wrap text-nowrap">
                            Rekapitulasi Rapor Evaluasi Dosen
                        </h5>
                        <div id="reportChart"></div>
                    </div>
                </div>
            </div>
        @endrole
    </div>
@endsection
