@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Form - Horizontal Layout')

@section('content')
<div class="row">
  <div class="col-xxl">
    <div class="card mb-6">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Tambah Kuesioner</h5>
        <small class="text-muted float-end">Form Horizontal</small>
      </div>
      <div class="card-body">
        <form action="{{ route('form.simpan') }}" method="POST">
          @csrf

          {{-- Kode Form --}}
          <div class="row mb-4">
            <label class="col-sm-2 col-form-label" for="code">Kode Form</label>
            <div class="col-sm-10">
              <input type="text" name="code" id="code" class="form-control" required>
            </div>
          </div>

          {{-- Judul Form --}}
          <div class="row mb-4">
            <label class="col-sm-2 col-form-label" for="title">Judul Form</label>
            <div class="col-sm-10">
              <input type="text" name="title" id="title" class="form-control" required>
            </div>
          </div>

          {{-- Tipe Form --}}
          <div class="row mb-4">
            <label class="col-sm-2 col-form-label" for="description">Tipe Form</label>
            <div class="col-sm-10">
              <select name="description" id="description" class="form-control" required>
                <option value="survei">Survei</option>
                <option value="kuesioner">Kuesioner</option>
              </select>
            </div>
          </div>

          {{-- Tanggal Mulai --}}
          <div class="row mb-4">
            <label class="col-sm-2 col-form-label" for="start_at">Tanggal Mulai</label>
            <div class="col-sm-10">
              <input type="date" name="start_at" id="start_at" class="form-control" required>
            </div>
          </div>

          {{-- Tanggal Selesai --}}
          <div class="row mb-4">
            <label class="col-sm-2 col-form-label" for="end_at">Tanggal Selesai</label>
            <div class="col-sm-10">
              <input type="date" name="end_at" id="end_at" class="form-control" required>
            </div>
          </div>

          <div class="row justify-content-end">
            <div class="col-sm-10">
              <button type="submit" class="btn btn-primary">Simpan Form</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
