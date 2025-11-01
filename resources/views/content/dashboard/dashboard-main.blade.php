@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
    <div class="row gy-6">
        @if ($activeForm && $activeForm->count() > 0)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-wrap">
                        <h5 class="card-title mb-0 flex-wrap text-wrap">
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
                </div>
            </div>
        @endif

        @role('student')
            @include('content.dashboard.partials.active-form')
        @endrole

        @role('superadmin|admin|direktur|wadir|kajur|kaprodi')
            @include('content.dashboard.partials.filter')
            @include('content.dashboard.partials.kpis')
            @include('content.dashboard.partials.chart-score-prodi')
            @include('content.dashboard.partials.chart-question-stack')
        @endrole

        @role('lecturer')
            @notrole('direktur|wadir|kajur|kaprodi')
                @include('content.dashboard.partials.chart-lecture-evaluation')
            @endnotrole
        @endrole
    </div>
@endsection
