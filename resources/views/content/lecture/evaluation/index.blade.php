@extends('layouts/contentNavbarLayout')

@section('title', 'Rapor Evaluasi Dosen')

@section('page-script')
    @vite('resources/assets/js/index-reports.js')
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
    <div class="card">
        <div class="row g-2 align-items-center my-3 mx-1">
            <div class="col-12 col-md">
                <form action="{{ route('form.index') }}" method="GET" id="form-filter">
                    <div class="row g-2 justify-content-md-end">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-sm">
                                <input type="search" class="form-control" placeholder="Cari..." aria-label="Cari..."
                                    id="search" name="search" value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Rata-rata Nilai</th>
                        <th>Predikat</th>
                        <th>Total Responden</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($reports as $index => $report)
                        <tr>
                            <td>
                                {{ $reports->firstItem() + $index }}
                            </td>
                            <td>
                                {{ $report->form->code }}
                            </td>
                            <td>
                                {{ $report->form->title }} @if ($report->form->trashed())
                                    <span class="badge bg-label-danger">Terhapus</span>
                                @endif
                            </td>
                            <td>
                                {{ $report->overall_average_score !== null ? number_format($report->overall_average_score, 2) : 'N/A' }}
                            </td>
                            <td>
                                {{ $report->predicate }}
                            </td>
                            <td>
                                {{ $report->total_respondents }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ route('lecture.evaluation.report.pdf', $report->id) }}"
                                            target="_blank">
                                            <i class="ri-file-download-line me-1"></i>
                                            Download Rapor Saya</a>
                                        @role('kajur')
                                            <a class="dropdown-item"
                                                href="{{ route('lecture.evaluation.report.pdf.all', $report->form->id) }}"
                                                target="_blank">
                                                <i class="ri-file-download-line me-1"></i>
                                                Download Semua Rapor</a>
                                        @endrole('kajur')
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($reports->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $reports->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
