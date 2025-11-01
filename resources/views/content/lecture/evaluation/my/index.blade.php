@extends('layouts/contentNavbarLayout')

@section('title', 'Rapor Evaluasi Dosen')

@section('page-script')
    @vite('resources/assets/js/index-report.js')
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Rapor Evaluasi Dosen</li>
        </ol>
    </nav>

    @include('content.lecture.evaluation.my.partials.table')
@endsection
