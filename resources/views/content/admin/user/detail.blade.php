@extends('layouts/contentNavbarLayout')

@section('title', 'Management User | Detail')

@section('content')
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-6">
                <div class="card-body pt-12">
                    <div class="user-avatar-section">
                        <div class=" d-flex align-items-center flex-column">
                            @if ($user->pp_path)
                                <img class="img-fluid rounded mb-4" src="{{ $user->photoUrl() }}" height="120" width="120"
                                    alt="User avatar">
                            @else
                                <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/1.png') }}"
                                    height="120" width="120" alt="User avatar">
                            @endif
                            <div class="user-info text-center">
                                <h5>{{ $user->name }}</h5>
                                <span class="badge bg-label-danger rounded-pill">{{ $user->role }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                        <div class="d-flex align-items-center me-5 gap-4">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="ri-money-dollar-circle-line ri-24px"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $historyTransactions->where('pg_status', 'success')->count() }}</h5>
                                <span>Transaction Success</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="ri-calendar-event-line ri-24px"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $historyTransactions->where('is_checked', 1)->count() }}</h5>
                                <span>Attend Event</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="pb-4 border-bottom mb-4">Details</h5>
                    <div class="info-container">
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="h6">Nama:</span>
                                <span>{{ $user->name }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Email:</span>
                                <span>{{ $user->email }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Jenis Kelamin:</span>
                                <span>{{ $user->gender }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Alamat:</span>
                                <span>{{ $user->address }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Negara:</span>
                                <span>{{ $user->country }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Provinsi:</span>
                                <span>{{ $user->state }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Kota:</span>
                                <span>{{ $user->city }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Status:</span>
                                @if ($user->is_active)
                                    <span class="badge rounded-pill bg-label-success me-1">Active</span>
                                @else
                                    <span class="badge rounded-pill bg-label-danger me-1">Inactive</span>
                                @endif
                            </li>
                            <li class="mb-2">
                                <span class="h6">Role:</span>
                                <span>{{ $user->role }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Nomor HP:</span>
                                <span>{{ $user->phone }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /User Card -->
        </div>
        <!--/ User Sidebar -->


        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- Invoice table -->
            <div class="card mb-4">
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <div class="card-header d-flex">
                            <div class="head-label">
                                <h5 class="card-title mb-0">Riwayat Transaksi</h5>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Inv</th>
                                    <th>Tanggal</th>
                                    <th>Event</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historyTransactions as $history)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-truncate h6 mb-0">
                                                    <a
                                                        href="{{ route('admin.t_event.detail', $history->id) }}">#{{ $history->invoice_number }}</a>
                                                </span>
                                                <small class="text-truncate">
                                                    <a href="{{ $history->ticket_path }}" target="_blank">Detail Tiket</a>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.event.detail', $history->eventItem->event->id) }}"
                                                target="_blank">{{ $history->eventItem->event->title }}</a>
                                        </td>
                                        <td>
                                            @if ($history->pg_status == 'pending')
                                                <h6 class="mb-0 w-px-100 d-flex align-items-center text-warning">
                                                    <i class="ri-circle-fill ri-10px me-1"></i>Pending
                                                </h6>
                                            @elseif ($history->pg_status == 'success')
                                                <h6 class="mb-0 w-px-100 d-flex align-items-center text-success">
                                                    <i class="ri-circle-fill ri-10px me-1"></i>Success
                                                </h6>
                                            @elseif ($history->pg_status == 'failed')
                                                <h6 class="mb-0 w-px-100 d-flex align-items-center text-danger">
                                                    <i class="ri-circle-fill ri-10px me-1"></i>Failed
                                                </h6>
                                            @elseif ($history->pg_status == 'cancel')
                                                <h6 class="mb-0 w-px-100 d-flex align-items-center text-secondary">
                                                    <i class="ri-circle-fill ri-10px me-1"></i>Cancel
                                                </h6>
                                            @elseif ($history->pg_status == 'expired')
                                                <h6 class="mb-0 w-px-100 d-flex align-items-center text-secondary">
                                                    <i class="ri-circle-fill ri-10px me-1"></i>Expired
                                                </h6>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($historyTransactions->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{ $historyTransactions->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
            <!-- /Invoice table -->
        </div>
        <!--/ User Content -->
    </div>
@endsection
