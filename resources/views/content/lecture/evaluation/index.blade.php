@extends('layouts/contentNavbarLayout')

@section('title', 'Rapor Evaluasi Dosen')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Rapor Evaluasi Dosen</li>
        </ol>
    </nav>

    @include('content.lecture.evaluation.partials.filter')

    <div class="card">
        <div class="row g-2 align-items-center my-3 mx-1">
            <div class="col-12 col-md">
                <div class="row g-2 justify-content-md-end">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="input-group input-group-sm">
                            <input type="search" class="form-control" placeholder="Cari..." aria-label="Cari..."
                                id="search" name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Rata-rata Nilai</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($lectures as $index => $lecture)
                        <tr>
                            <td>
                                {{ $lectures->firstItem() + $index }}
                            </td>
                            <td>
                                {{ $lecture->name }}
                            </td>
                            <td>
                                {{ $lecture->avg_score !== null ? number_format($lecture->avg_score, 2) : 'N/A' }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('lecture.evaluation.show', $lecture->id) }}"
                                            target="_blank">
                                            <i class="ri-dashboard-line me-1"></i>
                                            Dashboard</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($lectures->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $lectures->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
