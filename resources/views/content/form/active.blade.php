@extends('layouts/contentNavbarLayout')

@section('title', 'Form Aktif')

@section('page-script')
    @vite('resources/assets/js/index-forms.js')
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Form Aktif</li>
        </ol>
    </nav>
    <div class="card">
        <div class="row g-2 align-items-center my-3 mx-1">
            <div class="col-12 col-md">
                <form action="{{ route('form.active') }}" method="GET" id="form-filter">
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
                    @foreach ($forms as $index => $form)
                        <tr>
                            <td>
                                {{ $forms->firstItem() + $index }}
                            </td>
                            <td>
                                {{ $form->code }}
                            </td>
                            <td>
                                {{ $form->title }}
                            </td>
                            <td>
                                {{ $form->type }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('form.fill', ['id' => $form->id]) }}"><i
                                                class="ri-edit-line me-1"></i> Kerjakan Form</a>
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
