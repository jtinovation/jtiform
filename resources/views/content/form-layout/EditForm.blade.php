@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Form - Horizontal Layout')

@section('content')
    <div class="row">
        <div class="col-xxl">
            <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Edit Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('form.update', $form->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Form Code --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="code">Kode Form</label>
                            <div class="col-sm-10">
                                <input type="text" name="code" id="code"
                                    class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code', $form->code) }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Title --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="title">Judul Form</label>
                            <div class="col-sm-10">
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $form->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Type --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="type">Tipe Form</label>
                            <div class="col-sm-10">
                                <select name="type" id="type"
                                    class="form-control @error('type') is-invalid @enderror">
                                    <option value="">-- Select Form Type --</option>
                                    <option value="survey" {{ old('type', $form->type) == 'survey' ? 'selected' : '' }}>
                                        Survey</option>
                                    <option value="questionnaire"
                                        {{ old('type', $form->type) == 'questionnaire' ? 'selected' : '' }}>Questionnaire
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Description --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="description">Deskripsi</label>
                            <div class="col-sm-10">
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3" placeholder="Enter form description (optional)">{{ old('description', $form->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Start Date --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="start_at">Tanggal mulai</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="start_at" id="start_at"
                                    class="form-control @error('start_at') is-invalid @enderror"
                                    value="{{ old('start_at') ? \Carbon\Carbon::parse(old('start_at'))->format('Y-m-d\TH:i') : ($form->start_at ? \Carbon\Carbon::parse($form->start_at)->format('Y-m-d\TH:i') : '') }}">
                                @error('start_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- End Date --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="end_at">Tanggal berakhir</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="end_at" id="end_at"
                                    class="form-control @error('end_at') is-invalid @enderror"
                                    value="{{ old('end_at') ? \Carbon\Carbon::parse(old('end_at'))->format('Y-m-d\TH:i') : ($form->end_at ? \Carbon\Carbon::parse($form->end_at)->format('Y-m-d\TH:i') : '') }}">
                                @error('end_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Cover Path --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="cover_path">Cover Path</label>
                            <div class="col-sm-10">
                                <input type="text" name="cover_path" id="cover_path"
                                    class="form-control @error('cover_path') is-invalid @enderror"
                                    value="{{ old('cover_path', $form->cover_path) }}"
                                    placeholder="Enter cover path (optional)">
                                @error('cover_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Cover File --}}
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="cover_file">Cover File</label>
                            <div class="col-sm-10">
                                <input type="text" name="cover_file" id="cover_file"
                                    class="form-control @error('cover_file') is-invalid @enderror"
                                    value="{{ old('cover_file', $form->cover_file) }}"
                                    placeholder="Enter cover file (optional)">
                                @error('cover_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Update Form</button>
                                <a href="{{ url('/form') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
