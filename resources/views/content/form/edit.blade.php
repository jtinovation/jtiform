@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Form')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('form.index') }}">Management Form</a>
            </li>
            <li class="breadcrumb-item active">Edit Form</li>
        </ol>
    </nav>
    <div class="card mb-6">
        <form action="{{ route('form.update', $form->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body pt-0">
                <div class="row mt-1 g-5">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('code') is-invalid @enderror" type="text" id="code"
                                name="code" value="{{ old('code', $form->code) }}" placeholder="Masukkan Kode Form"
                                required />
                            <label for="code">Kode Form/Questionnaire</label>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('title') is-invalid @enderror" type="text" id="title"
                                name="title" value="{{ old('title', $form->title) }}"
                                placeholder="Masukkan Judul Form/Questionnaire" required />
                            <label for="title">Judul Form/Questionnaire</label>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('start_at') is-invalid @enderror" type="datetime-local"
                                id="start_at" name="start_at" value="{{ old('start_at', $form->start_at) }}"
                                placeholder="Masukkan Tanggal Mulai" required />
                            <label for="start_at">Tanggal Mulai</label>
                            @error('start_at')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('end_at') is-invalid @enderror" type="datetime-local"
                                id="end_at" name="end_at" value="{{ old('end_at', $form->end_at) }}"
                                placeholder="Masukkan Tanggal Berakhir" required />
                            <label for="end_at">Tanggal Berakhir</label>
                            @error('end_at')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror"
                                required>
                                <option value="" disabled selected>-- Pilih Tipe --</option>
                                <option value="form" {{ old('type', $form->type) == 'form' ? 'selected' : '' }}>Form
                                </option>
                                <option value="questionnaire"
                                    {{ old('type', $form->type) == 'questionnaire' ? 'selected' : '' }}>
                                    Questionnaire</option>
                            </select>
                            <label for="type">Tipe</label>
                            @error('type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control @error('cover') is-invalid @enderror" type="file" id="cover"
                                name="cover" value="{{ old('cover') }}" placeholder="Masukkan Cover" />
                            <label for="cover">Cover</label>
                            @error('cover')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                placeholder="Masukkan Deskripsi" style="height: 100px">{{ old('description', $form->description) }}</textarea>
                            <label for="description">Deskripsi</label>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-6">Simpan</button>
            </div>
        </form>
    </div>
@endsection
