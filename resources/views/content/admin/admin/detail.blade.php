@extends('layouts/contentNavbarLayout')

@section('title', 'Management User | Detail')

@section('content')
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-12 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-6">
                <div class="card-body pt-12">
                    <div class="user-avatar-section">
                        <div class=" d-flex align-items-center flex-column">
                            @if ($admin->pp_path)
                                <img class="img-fluid rounded mb-4" src="{{ asset($admin->photoUrl()) }}" height="120"
                                    width="120" alt="User avatar">
                            @else
                                <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/1.png') }}"
                                    height="120" width="120" alt="User avatar">
                            @endif
                            <div class="user-info text-center">
                                <h5>{{ $admin->name }}</h5>
                                <span class="badge bg-label-danger rounded-pill">{{ $admin->role }}</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="pb-4 border-bottom mb-4">Details</h5>
                    <div class="info-container">
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="h6">Nama:</span>
                                <span>{{ $admin->name }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Email:</span>
                                <span>{{ $admin->email }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Jenis Kelamin:</span>
                                <span>{{ $admin->gender }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Alamat:</span>
                                <span>{{ $admin->address }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Negara:</span>
                                <span>{{ $admin->country }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Provinsi:</span>
                                <span>{{ $admin->state }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Kota:</span>
                                <span>{{ $admin->city }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Status:</span>
                                @if ($admin->is_active)
                                    <span class="badge rounded-pill bg-label-success me-1">Active</span>
                                @else
                                    <span class="badge rounded-pill bg-label-danger me-1">Inactive</span>
                                @endif
                            </li>
                            <li class="mb-2">
                                <span class="h6">Role:</span>
                                <span>{{ $admin->role }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Nomor HP:</span>
                                <span>{{ $admin->phone }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /User Card -->
        </div>
        <!--/ User Sidebar -->
    </div>
@endsection
