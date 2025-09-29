@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

{{-- @section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@endsection --}}

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
    </div>
@endsection
