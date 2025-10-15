@extends('layouts/contentNavbarLayout')

@section('title', 'Management Form')

@section('page-script')
    @vite('resources/assets/js/index-forms.js')
@endsection

@php
    use App\Enums\FormTypeEnum;
@endphp

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Management Form</li>
        </ol>
    </nav>
    <div class="card">
        <div class="row g-2 align-items-center my-3 mx-1">
            <div class="col-12 col-md">
                <form action="{{ route('form.index') }}" method="GET" id="form-filter">
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

            <div class="col-12 col-md-auto">
                <div
                    class="d-flex flex-column flex-sm-row flex-md-nowrap justify-content-center justify-content-md-end gap-2">
                    <a href="{{ route('form.create') }}" class="btn btn-primary btn-sm w-100 w-md-auto">
                        <span class="d-none d-sm-inline">Buat Form</span>
                        <span class="d-inline d-sm-none">Buat</span>
                    </a>
                </div>
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
                    @foreach ($forms as $index => $form)
                        <tr>
                            <td>
                                {{ $forms->firstItem() + $index }}
                            </td>
                            <td>
                                {{ $form->code }}
                            </td>
                            <td>
                                {{ $form->title }} @if ($form->trashed())
                                    <span class="badge bg-label-danger">Terhapus</span>
                                @endif
                            </td>
                            <td>
                                {{ $form->type }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        @if ($form->type === FormTypeEnum::LECTURE_EVALUATION->value)
                                            <a class="dropdown-item" href="{{ route('form.generate.report', $form->id) }}">
                                                <i class="ri-file-chart-line me-1"></i>
                                                Generate Report</a>
                                        @endif
                                        <a class="dropdown-item" href="{{ route('form.show', $form->id) }}">
                                            <i class="ri-eye-line me-1"></i>
                                            Lihat</a>
                                        <a class="dropdown-item" href="{{ route('form.question.index', $form->id) }}">
                                            <i class="ri-question-answer-line me-1"></i>
                                            List Pertanyaan</a>
                                        <a class="dropdown-item" href="{{ route('form.edit', $form->id) }}">
                                            <i class="ri-pencil-line me-1"></i>
                                            Edit</a>
                                        @if ($form->trashed())
                                            <form action="{{ route('form.restore', ['id' => $form->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item"><i
                                                        class="ri-restart-line me-1"></i>
                                                    Restore</button>
                                            </form>
                                        @else
                                            <button class="dropdown-item button-swal" data-id="{{ $form->id }}"
                                                data-name="{{ $form->title }}"><i class="ri-delete-bin-6-line me-1"></i>
                                                Hapus</button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($forms->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $forms->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
