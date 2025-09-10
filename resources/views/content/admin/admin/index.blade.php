@extends('layouts/contentNavbarLayout')

@section('title', 'Management Admin')

@section('page-script')
    @vite('resources/assets/js/index-admin.js')
@endsection

@section('content')
    @php
        $listBg = ['bg-label-primary', 'bg-label-warning', 'bg-label-success'];
    @endphp

    <div class="card">
        <div class="row mx-1 my-3">
            <div class="col-md-12 col-12">
                <div class="d-flex align-items-center justify-content-md-end justify-content-center">
                    <div class="me-4">
                        <form action="{{ route('admin.admin.index') }}" method="GET" id="form-filter">
                            <label>
                                <input type="search" class="form-control form-control-sm" placeholder="Cari Berdasarkan Nama"
                                    id="search" name="search" value="{{ request('search') }}" />
                            </label>
                        </form>
                    </div>
                    <div class="add-new">
                        <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#modalCenter">
                            <i class="ri-add-line me-0 me-sm-1 d-inline-block d-sm-none"></i>
                            <span class="d-none d-sm-inline-block"> Tambah Admin </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>JK</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar me-2">
                                            @if ($admin->pp_path)
                                                <img src="{{ asset($admin->photoUrl()) }}" alt="Avatar"
                                                    class="rounded-circle">
                                            @else
                                                <span
                                                    class="avatar-initial rounded-circle {{ $listBg[array_rand($listBg)] }}">
                                                    {{ Illuminate\Support\Str::substr($admin->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="emp_name text-truncate h6 mb-0">{{ $admin->name }}</span>
                                        <small class="emp_post text-truncate">
                                            {{ $admin->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $admin->email }}
                            </td>
                            <td>
                                {{ $admin->gender }}
                            </td>
                            <td>
                                @if ($admin->is_active)
                                    <span class="badge rounded-pill bg-label-primary me-1">Active</span>
                                @else
                                    <span class="badge rounded-pill bg-label-danger me-1">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ route('admin.admin.detail', ['id' => $admin->id]) }}"><i
                                                class="ri-eye-line me-1"></i>
                                            Detail</a>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.admin.edit', ['id' => $admin->id]) }}"><i
                                                class="ri-pencil-line me-1"></i>
                                            Edit</a>
                                        <button class="dropdown-item button-swal" data-id="{{ $admin->id }}"
                                            data-name="{{ $admin->name }}"><i class="ri-delete-bin-6-line me-1"></i>
                                            Delete</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($admins->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $admins->links('vendor.pagination.bootstrap-5') }}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Tambah Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.admin.store') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="row g-4">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="name" class="form-control" name="name"
                                        placeholder="Masukkan Nama" required>
                                    <label for="name">Nama</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" id="email" class="form-control" name="email"
                                        placeholder="Masukkan Email" required>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
