@extends('layouts/contentNavbarLayout')

@section('title', 'Form - Form Master')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between">
      <div class="card-header-left d-flex flex-column">
        <h5 class="mb-5 me-3">Data Form</h5>
        <a href="{{ route('form.create') }}" class="btn btn-primary">
          <span class="tf-icons ri-add-line ri-20px me-1_5"></span>Tambah Form
        </a>
      </div>
      <div class="card-header-right">
        <form class="d-flex">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="tf-icons ri-search-line"></i></span>
            <input type="text" class="form-control search-input" name="search-input" data-target="#form-master-table" placeholder="Search..." />
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="dynamic-content-container">
    <div class="card-body py-0">
      <div id="form-master-table" class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>

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
                <td>{{$forms->firstItem() + $index}}.</td>
                <td> {{$form->code}} </td>
                <td> {{$form->form_type}} </td>
                <td> {{$form->title}} </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('form.edit', $form->id) }}"><i class="ri-pencil-line me-1"></i> Edit Form</a>

                        {{-- Tombol Delete pakai data-id untuk JS --}}
                        <form action="{{ route('form.delete', $form->id) }}"
                        method="POST"
                        style="display: inline;"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus form ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                            <i class="ri-delete-bin-6-line me-1"></i> Hapus
                          </button>
                        </form>

                        <a class="dropdown-item" href="{{ route('forms.questions.index', $form->id) }}">
                        <i class="ri-questionnaire-line me-1"></i> List Pertanyaan</a>

                      </div>
                  </div>
                </td>
              </tr>

        @empty
        <tr>
            <td colspan="5" class="text-center py-4">
                <p class="mb-0">Tidak ada form</p>
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

@section('page-script')
  <script src="{{ asset('vendor/flasher/jquery.min.js') }}"></script>
  @vite(['resources/assets/js/form-search.js'])
@endsection
