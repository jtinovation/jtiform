@extends('layouts/contentNavbarLayout')

@section('title', 'List Pertanyaan')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between">
      <div class="card-header-left d-flex flex-column">
        <h5 class="mb-5 me-3">List Pertanyaan</h5>
        <button type="button" class="btn btn-primary">
          <span class="tf-icons ri-add-line ri-20px me-1_5"></span>Tambah Pertanyaan
        </button>
      </div>
      <div class="card-header-right">
        <form class="d-flex">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="tf-icons ri-search-line"></i></span>
            <input type="text" class="form-control search-input" name="search-input" data-target="#question-table" placeholder="Search..." />
          </div>
        </form>
      </div>
    </div>
  </div>
  </button>

  <div class="dynamic-content-container">
    <div class="card-body py-0">
      <div id="question-table" class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Nomor</th>
              <th>Tipe Pertanyaan</th>
              <th>Pertanyaan</th>
              <th>Wajib Diisi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($questions as $index => $question)
            <tr>
              <td>{{$questions->firstItem() + $index}}.</td>
              <td> {{$question->type}} </td>
              <td> {{$question->question}} </td>
              <td>
                @if ($question->is_required)
                  Wajib
                @else
                  Tidak
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);"><i class="ri-pencil-line me-1"></i> Edit Pertanyaan</a>
                    <a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-6-line me-1"></i> Hapus Pertanyaan</a>
                  </div>
                </div>
              </td>
            </tr>

            @empty
            <tr>
                <td colspan="4" class="text-center py-4">
                    <p class="mb-0">Tidak ada pertanyaan</p>
                </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer d-flex justify-content-center">
      {{ $questions->links('vendor.pagination.bootstrap-5') }}
    </div>
  </div>
</div>

@endsection

@section('page-script')
  <script src="{{ asset('vendor/flasher/jquery.min.js') }}"></script>
  @vite(['resources/assets/js/form-search.js'])
@endsection
