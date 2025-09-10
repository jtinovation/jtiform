@extends('layouts/contentNavbarLayout')

@section('title', 'Management Event')

@section('page-script')
    @vite('resources/assets/js/index-event.js')
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
                        <form action="{{ route('admin.event.index') }}" method="GET" id="form-filter">
                            <label>
                                <input type="search" class="form-control form-control-sm"
                                    placeholder="Cari Berdasarkan Judul" id="search" name="search"
                                    value="{{ request('search') }}" />
                            </label>
                        </form>
                    </div>
                    <div class="add-new">
                        <a class="btn btn-primary waves-effect waves-light" href="{{ route('admin.event.create') }}">
                            <i class="ri-add-line me-0 me-sm-1 d-inline-block d-sm-none"></i>
                            <span class="d-none d-sm-inline-block"> Tambah Event </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Quota</th>
                        <th>Email CP</th>
                        <th>WA CP</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar me-2">
                                            @if ($event->thumbnailUrl())
                                                <img src="{{ $event->thumbnailUrl() }}" alt="Avatar"
                                                    class="rounded-circle">
                                            @else
                                                <span
                                                    class="avatar-initial rounded-circle {{ $listBg[array_rand($listBg)] }}">
                                                    {{ Illuminate\Support\Str::substr($event->title, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="emp_name text-truncate h6 mb-0">{{ $event->title }}</span>
                                        <small class="emp_post text-truncate">
                                            {{ $event->schedule }} -- {{ $event->venue }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $event->type }}
                            </td>
                            <td>
                                {{ $event->start_time }}
                            </td>
                            <td>
                                {{ $event->end_time }}
                            </td>
                            <td>
                                {{ $event->total_quota }}
                            </td>
                            <td>
                                {{ $event->cp_email }}
                            </td>
                            <td>
                                {{ $event->cp_phone }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ route('admin.event.detail', ['id' => $event->id]) }}"><i
                                                class="ri-eye-line me-1"></i>
                                            Detail</a>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.event.edit', ['id' => $event->id]) }}"><i
                                                class="ri-pencil-line me-1"></i>
                                            Edit</a>
                                        <button class="dropdown-item button-swal" data-id="{{ $event->id }}"
                                            data-title="{{ $event->title }}"><i class="ri-delete-bin-6-line me-1"></i>
                                            Delete</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($events->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $events->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
