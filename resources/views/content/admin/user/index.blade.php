@extends('layouts/contentNavbarLayout')

@section('title', 'Management User')

@section('page-script')
    @vite('resources/assets/js/index-users.js')
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
                        <form action="{{ route('admin.users.index') }}" method="GET" id="form-filter">
                            <label>
                                <input type="search" class="form-control form-control-sm" placeholder="Cari Berdasarkan Nama" id="search"
                                    name="search" value="{{ request('search') }}" />
                            </label>
                        </form>
                    </div>
                    {{-- <div class="add-new">
                        <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasAddUser">
                            <i class="ri-add-line me-0 me-sm-1 d-inline-block d-sm-none"></i>
                            <span class="d-none d-sm-inline-block"> Tambah User </span>
                        </button>
                    </div> --}}
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
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar me-2">
                                            @if ($user->pp_path)
                                                <img src="{{ $user->photoUrl() }}" alt="Avatar"
                                                    class="rounded-circle">
                                            @else
                                                <span
                                                    class="avatar-initial rounded-circle {{ $listBg[array_rand($listBg)] }}">
                                                    {{ Illuminate\Support\Str::substr($user->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="emp_name text-truncate h6 mb-0">{{ $user->name }}</span>
                                        <small class="emp_post text-truncate">
                                            {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $user->email }}
                            </td>
                            <td>
                                {{ $user->gender }}
                            </td>
                            <td>
                                @if ($user->is_active)
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
                                            href="{{ route('admin.users.detail', ['id' => $user->id]) }}"><i
                                                class="ri-eye-line me-1"></i>
                                            Detail</a>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.users.edit', ['id' => $user->id]) }}"><i
                                                class="ri-pencil-line me-1"></i>
                                            Edit</a>
                                        <button class="dropdown-item button-swal" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"><i class="ri-delete-bin-6-line me-1"></i>
                                            Delete</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($users->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $users->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
