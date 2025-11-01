@extends('layouts/contentNavbarLayout')

@section('title', 'Rapor Evaluasi Dosen ' . $user->name)

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
    <div class="row gy-6">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('lecture.evaluation.index') }}">Rapor Evaluasi Dosen</a>
                </li>
                <li class="breadcrumb-item active">Rapor Evaluasi {{ $user->name }}</li>
            </ol>
        </nav>

        @include('content.dashboard.partials.chart-lecture-evaluation')
    </div>
@endsection
