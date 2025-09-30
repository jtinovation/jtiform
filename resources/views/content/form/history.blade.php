@extends('layouts/contentNavbarLayout')

@section('title', 'Riwayat Pengisian Form')

@php
    use App\Enums\FormTypeEnum;
@endphp

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#search').on('keyup', function() {
                let debounceTimer;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    $('#form-filter').submit();
                }, 1000);
            });
        });
    </script>
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Riwayat Pengisian Form</li>
        </ol>
    </nav>
    <div class="card">
        <div class="row g-2 align-items-center my-3 mx-1">
            <div class="col-12 col-md">
                <form action="{{ route('form.history') }}" method="GET" id="form-filter">
                    <div class="row g-2 justify-content-md-end">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-sm">
                                <input type="search" class="form-control" placeholder="Cari berdasarkan nama"
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
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($submissions as $index => $submission)
                        <tr>
                            <td>
                                {{ $submissions->firstItem() + $index }}
                            </td>
                            <td>
                                {{ $submission->form->code }}
                            </td>
                            <td>
                                {{ $submission->form->title }}
                            </td>
                            <td>
                                {{ $submission->form->type }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        @if ($submission->form->type === FormTypeEnum::GENERAL->value)
                                            <a class="dropdown-item"
                                                href="{{ route('form.result', ['id' => $submission->form->id]) }}"><i
                                                    class="ri-edit-line me-1"></i> Lihat Detail</a>
                                        @else
                                            <a class="dropdown-item"
                                                href="{{ route('form.result.evaluation', ['id' => $submission->form->id]) }}"><i
                                                    class="ri-edit-line me-1"></i> Lihat Detail</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($submissions->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $submissions->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
