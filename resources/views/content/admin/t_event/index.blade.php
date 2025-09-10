@extends('layouts/contentNavbarLayout')

@section('title', 'Management Transaksi')

@section('page-script')
    @vite('resources/assets/js/index-tevent.js')
@endsection

@section('content')
    @php
        $listBg = ['bg-label-primary', 'bg-label-warning', 'bg-label-success'];
    @endphp
    <div class="card mb-6">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">{{ $headerCount['pending'] }}</h4>
                                <p class="mb-0">Pending Payment</p>
                            </div>
                            <div class="avatar me-sm-6">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="ri-calendar-2-line ri-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-6">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">{{ $headerCount['success'] }}</h4>
                                <p class="mb-0">Success</p>
                            </div>
                            <div class="avatar me-lg-6">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="ri-check-double-line ri-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
                            <div>
                                <h4 class="mb-0">{{ $headerCount['cancelExpired'] }}</h4>
                                <p class="mb-0">Cancel/Expired</p>
                            </div>
                            <div class="avatar me-sm-6">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="ri-wallet-3-line ri-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-0">{{ $headerCount['failed'] }}</h4>
                                <p class="mb-0">Failed</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="ri-error-warning-line ri-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="row mx-1 my-3">
            <div class="col-md-12 col-12">
                <form action="{{ route('admin.t_event.index') }}" method="GET" id="form-filter">
                    <div class="d-flex align-items-center justify-content-md-end justify-content-center">
                        <div class="me-4">
                            <label>
                                <input type="search" class="form-control form-control-sm" id="search"
                                    placeholder="Cari Berdasarkan Inv" name="search" value="{{ request('search') }}" />
                            </label>
                        </div>
                        <div class="me-4">
                            <label>
                                <select name="filter_by_event" class="form-control form-control-sm" id="filter_by_event">
                                    <option value="">Pilih Event</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event->id }}"
                                            {{ request('filter_by_event') == $event->id ? 'selected' : '' }}>
                                            {{ $event->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Inv</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Checked</th>
                        <th>Method</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($tEvents as $tEvent)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-truncate h6 mb-0">
                                        <a
                                            href="{{ route('admin.t_event.detail', $tEvent->id) }}">#{{ $tEvent->invoice_number }}</a>
                                    </span>
                                    <small class="text-truncate">
                                        <a href="{{ $tEvent->ticket_path }}" target="_blank">Detail Tiket</a>
                                    </small>
                                </div>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($tEvent->created_at)->format('d M Y H:i') }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar me-2">
                                            @if ($tEvent->user->pp_path)
                                                <img src="{{ asset($tEvent->user->pp_path) }}" alt="Avatar"
                                                    class="rounded-circle">
                                            @else
                                                <span
                                                    class="avatar-initial rounded-circle {{ $listBg[array_rand($listBg)] }}">
                                                    {{ Illuminate\Support\Str::substr($tEvent->user->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="emp_name text-truncate h6 mb-0"><a
                                                href="{{ route('admin.users.detail', $tEvent->user->id) }}"
                                                target="_blank">{{ $tEvent->user->name }}</a></span>
                                        <small class="emp_post text-truncate">
                                            {{ $tEvent->user->email }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.event.detail', $tEvent->eventItem->event->id) }}"
                                    target="_blank">{{ $tEvent->eventItem->event->title }}</a>
                            </td>
                            <td>
                                @if ($tEvent->pg_status == 'pending')
                                    <h6 class="mb-0 w-px-100 d-flex align-items-center text-warning">
                                        <i class="ri-circle-fill ri-10px me-1"></i>Pending
                                    </h6>
                                @elseif ($tEvent->pg_status == 'success')
                                    <h6 class="mb-0 w-px-100 d-flex align-items-center text-success">
                                        <i class="ri-circle-fill ri-10px me-1"></i>Success
                                    </h6>
                                @elseif ($tEvent->pg_status == 'failed')
                                    <h6 class="mb-0 w-px-100 d-flex align-items-center text-danger">
                                        <i class="ri-circle-fill ri-10px me-1"></i>Failed
                                    </h6>
                                @elseif ($tEvent->pg_status == 'cancel')
                                    <h6 class="mb-0 w-px-100 d-flex align-items-center text-secondary">
                                        <i class="ri-circle-fill ri-10px me-1"></i>Cancel
                                    </h6>
                                @elseif ($tEvent->pg_status == 'expired')
                                    <h6 class="mb-0 w-px-100 d-flex align-items-center text-secondary">
                                        <i class="ri-circle-fill ri-10px me-1"></i>Expired
                                    </h6>
                                @endif
                            </td>

                            <td>
                                @if ($tEvent->is_checked == 1)
                                    <span class="badge rounded-pill bg-label-info me-1">Sudah Checkin</span>
                                @else
                                    <span class="badge rounded-pill bg-label-danger me-1">Belum Checkin</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ $tEvent->pg_invoice_url }}" target="_blank">{{ $tEvent->payment_method }}</a>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.t_event.detail', $tEvent->id) }}">
                                            <i class="ri-eye-line me-1"></i>
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($tEvents->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $tEvents->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
