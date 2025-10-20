@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@php
    function trendBadge($pct)
    {
        if (is_null($pct)) {
            return '<span class="badge bg-label-secondary">–</span>';
        }
        $icon = $pct >= 0 ? 'icon-base ri ri-arrow-up-s-line' : 'icon-base ri ri-arrow-down-s-line';
        $cls = $pct >= 0 ? 'bg-label-success' : 'bg-label-danger';
        return '<span class="badge ' .
            $cls .
            ' d-inline-flex align-items-center gap-1"><i class="' .
            $icon .
            '"></i> ' .
            number_format(abs($pct), 1) .
            '%</span>';
    }
@endphp

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

        @role('superadmin|admin|direktur|wadir|kajur|kaprodi')
            @include('content.dashboard.partials.filter')
            @include('content.dashboard.partials.kpis')
            @include('content.dashboard.partials.chart-score-prodi')
            @include('content.dashboard.partials.chart-question-stack')
        @endrole

        @role('lecturer')
            @include('content.dashboard.partials.chart-lecture-evaluation')
        @endrole
    </div>
@endsection
