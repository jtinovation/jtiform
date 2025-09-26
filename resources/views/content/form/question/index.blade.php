@extends('layouts/contentNavbarLayout')

@section('title', 'List Pertanyaan')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('form.index') }}">Management Form</a>
            </li>
            <li class="breadcrumb-item active">List Pertanyaan</li>
        </ol>
    </nav>
    <div class="card">
        <div class="row g-2 align-items-center my-3 mx-1">
            <div class="col-12 col-md">
                <form action="{{ route('form.question.index', $form->id) }}" method="GET" id="form-filter">
                    <div class="row g-2 justify-content-md-end">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-sm">
                                <input type="search" class="form-control" placeholder="Cari..." id="search"
                                    name="search" value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if ($questions->isEmpty())
                <div class="col-12 col-md-auto">
                    <div
                        class="d-flex flex-column flex-sm-row flex-md-nowrap justify-content-center justify-content-md-end gap-2">
                        <a href="{{ route('form.question.create', $form->id) }}"
                            class="btn btn-primary btn-sm w-100 w-md-auto">
                            <span class="d-none d-sm-inline">Tambah Pertanyaan</span>
                            <span class="d-inline d-sm-none">Tambah</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="col-12 col-md-auto">
                    <div
                        class="d-flex flex-column flex-sm-row flex-md-nowrap justify-content-center justify-content-md-end gap-2">
                        <a href="{{ route('form.question.edit', [$form->id]) }}"
                            class="btn btn-primary btn-sm w-100 w-md-auto">
                            <span class="d-none d-sm-inline">Edit Pertanyaan</span>
                            <span class="d-inline d-sm-none">Edit</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tipe Pertanyaan</th>
                        <th>Pertanyaan</th>
                        <th>Opsi</th>
                        <th>Wajib Diisi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($questions as $index => $question)
                        <tr>
                            <td>{{ $questions->firstItem() + $index }}</td>
                            <td> {{ $question->type }} </td>
                            <td> {{ $question->question }} </td>
                            <td>
                                @if (in_array($question->type, ['option', 'checkbox']))
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Lihat Opsi"
                                        onclick="showOptions({{ $question->options->toJson() }})">
                                        Lihat Opsi
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($question->is_required)
                                    <span class="badge bg-label-primary me-1">Wajib</span>
                                @else
                                    <span class="badge bg-label-secondary me-1">Tidak</span>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <p class="mb-0">Tidak ada pertanyaan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $questions->links('vendor.pagination.bootstrap-5') }}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalOptions" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalOptionsTitle">Opsi Pertanyaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group" id="optionsList">
                        <!-- Options will be populated here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    @vite(['resources/assets/js/index-forms-questions.js'])

    <script>
        function showOptions(options) {
            const optionsList = document.getElementById('optionsList');
            optionsList.innerHTML = ''; // Clear previous options

            options.forEach(option => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                listItem.textContent = option.sequence + '. ' + option.answer;
                if (option.point !== undefined && option.point !== null) {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-primary rounded-pill ms-2';
                    badge.textContent = option.point;
                    listItem.appendChild(badge);
                }
                optionsList.appendChild(listItem);
            });

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('modalOptions'));
            modal.show();
        }
    </script>
@endsection
