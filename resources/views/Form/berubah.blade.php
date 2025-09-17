@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Kuesioner</h2>

    <form action="{{ route('form.simpan') }}" method="POST">
        @csrf

        {{-- Kode Form --}}
        <div class="mb-3">
            <label for="code">Kode Form</label>
            <input type="text" name="code" class="form-control" required>
        </div>

        {{-- Judul Form --}}
        <div class="mb-3">
            <label for="title">Judul Form</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        {{-- Tipe Form (disimpan di description) --}}
        <div class="mb-3">
            <label for="description">Tipe Form</label>
            <select name="description" class="form-control" required>
                <option value="survei">Survei</option>
                <option value="kuesioner">Kuesioner</option>
            </select>
        </div>

        {{-- Tanggal Mulai --}}
        <div class="mb-3">
            <label for="start_at">Tanggal Mulai</label>
            <input type="date" name="start_at" class="form-control" required>
        </div>

        {{-- Tanggal Selesai --}}
        <div class="mb-3">
            <label for="end_at">Tanggal Selesai</label>
            <input type="date" name="end_at" class="form-control" required>
        </div>

        <hr>

        <button type="submit" class="btn btn-primary">Simpan Form</button>
    </form>
</div>
@endsection
