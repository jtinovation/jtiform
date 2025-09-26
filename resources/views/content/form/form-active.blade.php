@extends('layouts/contentNavbarLayout')

@section('title', 'Form - Form Aktif')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Form Aktif</h5>
            <form class="d-flex">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="tf-icons ri-search-line"></i></span>
                    <input type="text" class="form-control search-input" name="search-input"
                        data-target="#form-active-table" placeholder="Search..." />
                </div>
            </form>
        </div>

        <div class="dynamic-content-container">
            <div class="card-body py-0">
                <div id="form-active-table" class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Kode Form</th>
                                <th>Tipe Form</th>
                                <th>Judul Form</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($forms as $index => $form)
                                <tr>
                                    <td>{{ $forms->firstItem() + $index }}.</td>
                                    <td> {{ $form->code }} </td>
                                    <td> {{ $form->type }} </td>
                                    <td> {{ $form->title }} </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="ri-pencil-line me-1"></i> Mengerjakan Form</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="mb-0">Tidak ada form aktif</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $forms->links('vendor.pagination.bootstrap-5') }}
            </div>

        </div>
    </div>

@endsection

@section('page-script')
    <script src="{{ asset('vendor/flasher/jquery.min.js') }}"></script>
    @vite(['resources/assets/js/form-search.js'])
@endsection
