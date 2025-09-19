@extends('layouts/contentNavbarLayout')

@section('title', 'Form - Form Aktif')

@section('content')
<div class="card">
  <h5 class="card-header">Data Form</h5>
  <div class="card-body">

    {{-- Tombol Tambah Form --}}
    <a href="{{ route('form.tambah') }}" class="btn btn-primary">
      <span class="tf-icons ri-add-line ri-20px me-1_5"></span> Tambah Form
    </a>

    <div class="table-responsive text-nowrap mt-3">
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
            <tr id="form-row-{{ $form->id }}">
              <td>{{ $index + 1 }}.</td>
              <td>{{ $form->code }}</td>
              <td>{{ $form->form_type }}</td>
              <td>{{ $form->title }}</td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="ri-more-2-line"></i>
                  </button>
                  <div class="dropdown-menu">

                    {{-- Tombol Edit Form --}}
                    <a class="dropdown-item" href="{{ route('form.edit', $form->id) }}">
                      <i class="ri-pencil-line me-1"></i> Edit Form
                    </a>

                    {{-- Tombol Delete pakai data-id untuk JS --}}
                    <form action="{{ route('form.hapus', $form->id) }}"
                    method="POST"
                    style="display: inline;"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus form ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                        <i class="ri-delete-bin-6-line me-1"></i> Delete
                      </button>
                    </form>

                    {{-- Link List Pertanyaan --}}
                    <a class="dropdown-item" href="{{ route('form.questions', ['id' => $form->id]) }}">
                      <i class="ri-questionnaire-line me-1"></i> List Pertanyaan
                    </a>
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
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
