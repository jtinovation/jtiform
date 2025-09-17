@extends('layouts/contentNavbarLayout')

@section('title', 'List Pertanyaan')

@section('content')
<div class="card">
  <h5 class="card-header">List Pertanyaan</h5>
  <div class="card-body">
    <button type="button" class="btn btn-primary">
    <span class="tf-icons ri-add-line ri-20px me-1_5"></span>Tambah Data Pertanyaan
  </div>
  </button>
  <div class="table-responsive text-nowrap">
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
          <td>{{$index + 1}}.</td>
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
                <a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-6-line me-1"></i> Delete</a>
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

        {{-- <tr>
          <td>1.</td>
          <td>KUISIONER-2025</td>
          <td>lorem ipsum</td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="javascript:void(0);"><i class="ri-pencil-line me-1"></i> Edit</a>
                <a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-6-line me-1"></i> Delete</a>
              </div>
            </div>
          </td>
        </tr> --}}

      </tbody>
    </table>
  </div>
</div>

@endsection
