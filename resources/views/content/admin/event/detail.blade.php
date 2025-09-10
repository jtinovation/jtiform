@extends('layouts/contentNavbarLayout')

@section('title', 'Management User | Detail')

@section('page-script')
    @vite('resources/assets/js/detail-event.js')
@endsection

@section('content')
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-6">
                <div class="card-body pt-12">
                    <div class="user-avatar-section">
                        <div class=" d-flex align-items-center flex-column">
                            @php
                                $banner = $event->galleries->where('is_banner', true)->first();
                                $bannerUrl = $banner->imageUrl() ?? null;
                            @endphp
                            @if ($bannerUrl)
                                <img class="img-fluid rounded mb-4" src="{{ $bannerUrl }}" width="100%" alt="Banner">
                            @else
                                <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/1.png') }}"
                                    height="120" width="120" alt="User avatar">
                            @endif
                            <div class="user-info text-center">
                                <h5>{{ $event->name }}</h5>
                                <span class="badge bg-label-danger rounded-pill">{{ $event->role }}</span>
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
                                <h5 class="mb-0">{{ $countSuccess }}</h5>
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
                                <h5 class="mb-0">{{ $countAttended }}</h5>
                                <span>Attend Event</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="pb-4 border-bottom mb-4">Details</h5>
                    <div class="info-container">
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="h6">Judul:</span>
                                <span>{{ $event->title }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Tipe:</span>
                                <span>{{ $event->type }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Venue:</span>
                                <span>{{ $event->venue }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Venue Gmaps:</span>
                                <span><a href="{{ $event->venue_maps_url }}" target="_blank">Open in Maps</a></span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Tanggal:</span>
                                <span>{{ $event->schedule }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Jam:</span>
                                <span>{{ $event->start_time }} - {{ $event->end_time }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Total Quota:</span>
                                <span>{{ $event->total_quota }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">CP Email:</span>
                                <span>{{ $event->cp_email }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">CP Wa:</span>
                                <span>{{ $event->cp_phone }}</span>
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
            {{-- deskripsi --}}
            <div class="card mb-4">
                <h5 class="card-header">Deskripsi Event</h5>
                <div class="card-body pt-0">
                    {!! $event->description !!}
                </div>
            </div>

            <!-- Invoice table -->
            <div class="card mb-4">
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <div class="card-header d-flex justify-content-between">
                            <div class="head-label">
                                <h5 class="card-title mb-0">Event Item</h5>
                            </div>
                            <div class="add-new">
                                <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                                    data-bs-target="#modalCenter">
                                    <i class="ri-add-line me-0 me-sm-1 d-inline-block d-sm-none"></i>
                                    <span class="d-none d-sm-inline-block"> Tambah Item </span>
                                </button>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Quota</th>
                                    <th>Harga</th>
                                    <th>Total terjual</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($eventItems as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->quota }}</td>
                                        <td>@currency($item->price)</td>
                                        <td>{{ $item->tEvent->where('pg_status', 'success')->count() }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                                <div class="dropdown-menu">
                                                    <button class="dropdown-item button-swal" data-id="{{ $item->id }}"
                                                        data-title="{{ $item->title }}"><i
                                                            class="ri-delete-bin-6-line me-1"></i>
                                                        Delete</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($eventItems->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{ $eventItems->links('vendor.pagination.bootstrap-5') }}

                </div>
            </div>
            <!-- /Invoice table -->

            <div class="card mb-4">
                <h5 class="card-header">Gallery</h5>
                <div class="card-body pt-0">
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach ($event->galleries as $gallery)
                                <button type="button" data-bs-target="#carouselExample"
                                    data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"
                                    aria-label="Slide {{ $loop->index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($event->galleries as $gallery)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <button type="button"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 p-1 rounded-circle btn-delete-gallery"
                                        style="z-index: 2;" data-id="{{ $gallery->id }}" data-toggle="tooltip"
                                        title="Hapus gambar">
                                        <i class="ri-delete-bin-6-line"></i>
                                    </button>
                                    <img class="d-block w-100" src="{{ $gallery->imageUrl() }}" alt="Gallery" />
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#carouselExample" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExample" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--/ User Content -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Tambah Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.event.item.store') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="m_event_id" id="m_event_id" value="{{ $event->id }}">
                        <div class="row">
                            <div class="col mb-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="title" class="form-control" placeholder="Judul Item"
                                        name="title">
                                    <label for="name">Nama</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="quota" class="form-control" name="quota"
                                        placeholder="Enter Quota">
                                    <label for="quota">Quota</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="price" class="form-control" name="price"
                                        placeholder="Enter Price">
                                    <label for="price">Price</label>
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
