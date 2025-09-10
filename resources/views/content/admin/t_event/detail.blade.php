@extends('layouts/contentNavbarLayout')

@section('title', 'Management Transaksi | Detail')

@section('content')
    @php
        $listBg = ['bg-label-primary', 'bg-label-warning', 'bg-label-success'];
    @endphp
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 gap-3">

        <div class="d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center mb-1">
                <h5 class="mb-0">Order #{{ $tEvent->invoice_number }}</h5>
                @if ($tEvent->pg_status == 'pending')
                    <span class="badge bg-label-warning me-2 ms-2 rounded-pill">Pending</span>
                @elseif ($tEvent->pg_status == 'success')
                    <span class="badge bg-label-success me-2 ms-2 rounded-pill">Success</span>
                @elseif ($tEvent->pg_status == 'failed')
                    <span class="badge bg-label-danger me-2 ms-2 rounded-pill">Failed</span>
                @elseif ($tEvent->pg_status == 'cancel')
                    <span class="badge bg-label-secondary me-2 ms-2 rounded-pill">Cancel</span>
                @elseif ($tEvent->pg_status == 'expired')
                    <span class="badge bg-label-secondary me-2 ms-2 rounded-pill">Expired</span>
                @endif
            </div>
            <p class="mt-1 mb-3">
                {{ \Carbon\Carbon::parse($tEvent->created_at)->format('d M Y H:i') }}
            </p>
        </div>
        {{-- <div class="d-flex align-content-center flex-wrap gap-2">
            <button class="btn btn-outline-danger delete-order waves-effect">Hapus Order</button>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Detail Transaksi</h5>
                </div>
                <div class="table-responsive pb-3">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="datatables-order-details table dataTable no-footer dtr-column">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Harga</th>
                                    <th>qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center product-name">
                                            <div class="d-flex flex-column">
                                                <span class="text-nowrap text-heading fw-medium">
                                                    {{ $tEvent->eventItem->title }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @currency($tEvent->eventItem->price)
                                    </td>
                                    <td>1</td>
                                    <td>
                                        @currency($tEvent->eventItem->price)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="width: 1%;"></div>
                    </div>
                    <div class="d-flex justify-content-end align-items-center m-4 p-1 mb-0 pb-0">
                        <div class="order-calculations">
                            <div class="d-flex justify-content-start gap-4 mb-2">
                                <span class="w-px-100 text-heading">Subtotal:</span>
                                <h6 class="mb-0">
                                    @currency($tEvent->amount)
                                </h6>
                            </div>
                            <div class="d-flex justify-content-start gap-4 mb-2">
                                <span class="w-px-100 text-heading">Fee:</span>
                                <h6 class="mb-0">
                                    @currency($tEvent->pg_fee)
                                </h6>
                            </div>
                            <div class="d-flex justify-content-start gap-4 mb-2">
                                <span class="w-px-100 text-heading">Tax:</span>
                                <h6 class="mb-0">
                                    @currency($tEvent->pg_tax)
                                </h6>
                            </div>
                            <div class="d-flex justify-content-start gap-4">
                                <h6 class="w-px-100 mb-0">Total:</h6>
                                <h6 class="mb-0">
                                    @currency($tEvent->total)
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Detail Tiket</h5>
                </div>
                <div class="card-body">
                    <embed src="https://pearl.nmsu.edu/sample-pages/Cascade-CMS-ProcessFlow.pdf" type="application/pdf"
                        width="100%" height="600px" />
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-6">
                <div class="card-body">
                    <h5 class="card-title mb-6">Detail Customer</h5>
                    <div class="d-flex justify-content-start align-items-center mb-6">
                        <div class="avatar me-3">
                            @if ($tEvent->user->pp_path)
                                <img src="{{ asset($tEvent->user->pp_path) }}" alt="Avatar" class="rounded-circle">
                            @else
                                <span class="avatar-initial rounded-circle {{ $listBg[array_rand($listBg)] }}">
                                    {{ Illuminate\Support\Str::substr($tEvent->user->name, 0, 2) }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex flex-column">
                            <a href="{{ route('admin.users.detail', ['id' => $tEvent->user->id]) }}" target="_blank">
                                <h6 class="mb-0">
                                    {{ $tEvent->user->name }}
                                </h6>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">Contact info</h6>
                    </div>
                    <p class="mb-1">Email: {{ $tEvent->user->email }}</p>
                    <p class="mb-0">Mobile: {{ $tEvent->user->phone }}</p>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-1">Detail Payment</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">Method: {{ $tEvent->payment_method }}</p>
                    <p class="mb-0">Reff Id: {{ $tEvent->pg_reff_id }}</p>
                    <p class="mb-0">Invoice: <a href="{{ $tEvent->pg_invoice_url }}">View Invoice</a></p>
                    <p class="mb-0">Urutan order: {{ $tEvent->queue_number }}</p>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-1">Tiket</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">Tanggal Berlaku:
                        {{ \Carbon\Carbon::parse($tEvent->available_at)->format('d M Y H:i') }}</p>
                    <p class="mb-0">Tanggal Kadaluarsa:
                        {{ \Carbon\Carbon::parse($tEvent->expired_at)->format('d M Y H:i') }}</p>
                    <p class="mb-0">Sudah check-in: {{ $tEvent->is_checked ? 'Ya' : 'Belum' }}</p>
                    <div class="mt-3 text-center">
                        {{ $qr }}
                        <br>
                        {{ $tEvent->ticket_code }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
