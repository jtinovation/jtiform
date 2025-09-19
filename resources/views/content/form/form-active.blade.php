@extends('layouts/contentNavbarLayout')

@section('title', 'Form - Form Aktif')

@section('content')
<div class="card">
  <h5 class="card-header">Data Form Aktif</h5>
  <div class="table-responsive text-nowrap">
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
          <td>{{$index + 1}}.</td>
          <td> {{$form->code}} </td>
          <td> {{$form->form_type}} </td>
          <td> {{$form->title}} </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
              <div class="dropdown-menu">

               <!-- Edit Form -->
               <a class="dropdown-item" href="{{ route('form.edit', $form->id) }}">
               <i class="ri-pencil-line me-1"></i> Edit Form</a>

                <!-- Ganti dengan form langsung -->
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


                <a class="dropdown-item" href=" {{route('form.questions', ['id' => $form->id])}}" ><i class="ri-questionnaire-line me-1"></i> List Pertanyaan</a>
              </div>
            </div>
          </td>
        </tr>

        @empty
        <tr>
            <td colspan="4" class="text-center py-4">
                <p class="mb-0">Tidak ada form aktif</p>
            </td>
        </tr>
        @endforelse

      </tbody>
    </table>
  </div>
</div>

@endsection
