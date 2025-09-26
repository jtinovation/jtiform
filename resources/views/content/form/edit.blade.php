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
                                id="start_at" name="start_at" value="{{ old('start_at', \Carbon\Carbon::parse($form->start_at)->format('Y-m-d\TH:i')) }}"
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
                                id="end_at" name="end_at" value="{{ old('end_at', \Carbon\Carbon::parse($form->end_at)->format('Y-m-d\TH:i')) }}"
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
                                <option value="" disabled {{ old('type', $form->type) ? '' : 'selected' }}>-- Pilih Tipe --</option>
                                <option value="form" {{ old('type', $form->type) == 'form' ? 'selected' : '' }}>General</option>
                                <option value="questionnaire" {{ old('type', $form->type) == 'questionnaire' ? 'selected' : '' }}>Lecture Evaluation</option>
                            </select>
                            <label for="type">Tipe</label>
                            @error('type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- 🔹 Tambahan Dinamis --}}
                    <div class="col-md-6 {{ old('type', $form->type) == 'form' ? '' : 'd-none' }}" id="responden-wrapper">
                        <div class="form-floating form-floating-outline">
                            <select name="responden" id="responden" class="form-select">
                                <option value="" disabled {{ old('responden', $form->responden) ? '' : 'selected' }}>-- Pilih Responden --</option>
                                <option value="semua" {{ old('responden', $form->responden) == 'semua' ? 'selected' : '' }}>Semua</option>
                                <option value="jurusan" {{ old('responden', $form->responden) == 'jurusan' ? 'selected' : '' }}>Jurusan</option>
                                <option value="mahasiswa" {{ old('responden', $form->responden) == 'mahasiswa' ? 'selected' : '' }}>Spesifik Mahasiswa</option>
                                <option value="dosen" {{ old('responden', $form->responden) == 'dosen' ? 'selected' : '' }}>Spesifik Dosen</option>
                                <option value="tendik" {{ old('responden', $form->responden) == 'tendik' ? 'selected' : '' }}>Spesifik Tenaga Pendidik</option>
                            </select>
                            <label for="responden">Responden</label>
                        </div>
                    </div>

                    {{-- 🔹 Jurusan --}}
                    <div class="col-md-6 {{ in_array(old('responden', $form->responden), ['jurusan','mahasiswa','dosen','tendik']) ? '' : 'd-none' }}" id="jurusan-wrapper">
                        <div class="form-floating form-floating-outline">
                            <select name="jurusan" id="jurusan" class="form-select">
                                <option value="" disabled {{ old('jurusan', $form->jurusan) ? '' : 'selected' }}>-- Pilih Jurusan --</option>
                                <option value="ti" {{ old('jurusan', $form->jurusan) == 'ti' ? 'selected' : '' }}>Teknologi Informasi</option>
                            </select>
                            <label for="jurusan">Jurusan</label>
                        </div>
                    </div>

                    {{-- 🔹 Select Prodi --}}
                    <div class="col-md-6 {{ old('responden', $form->responden) == 'jurusan' ? '' : 'd-none' }}" id="prodi-wrapper">
                        <div class="form-floating form-floating-outline">
                            <select name="prodi" id="prodi" class="form-select">
                                <option value="" disabled {{ old('prodi', $form->prodi) ? '' : 'selected' }}>-- Pilih Prodi --</option>
                                @if(old('jurusan', $form->jurusan) && isset($form->prodi))
                                    <option value="{{ $form->prodi }}" selected>{{ $form->prodi }}</option>
                                @endif
                            </select>
                            <label for="prodi">Program Studi</label>
                        </div>
                    </div>
                    {{-- 🔹 End Tambahan --}}

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

   {{-- 🔹 Script jQuery --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
        const prodiData = {
            ti: ['D3 Manajemen Informatika', 'D4 Teknik Informatika', 'D3 Teknik Komputer']
        };

        // 🔹 Tampilkan Responden jika pilih "form"
        $('#type').on('change', function() {
            if ($(this).val() === 'form') {
                $('#responden-wrapper').removeClass('d-none');
            } else {
                $('#responden-wrapper').addClass('d-none');
                $('#jurusan-wrapper').addClass('d-none');
                $('#prodi-wrapper').addClass('d-none');
            }
        });

        // 🔹 Logika responden
        $('#responden').on('change', function() {
            let val = $(this).val();

            if (val === 'jurusan' || val === 'mahasiswa' || val === 'dosen' || val === 'tendik') {
                $('#jurusan-wrapper').removeClass('d-none');
                $('#prodi-wrapper').addClass('d-none');
            }
            else {
                $('#jurusan-wrapper').addClass('d-none');
                $('#prodi-wrapper').addClass('d-none');
            }
        });

        // 🔹 Tampilkan Prodi hanya jika responden = jurusan
        $('#jurusan').on('change', function() {
            let jurusan = $(this).val();
            let responden = $('#responden').val();
            let $prodi = $('#prodi');

            $prodi.empty().append('<option value="" disabled selected>-- Pilih Prodi --</option>');

            if (responden === 'jurusan' && prodiData[jurusan]) {
                prodiData[jurusan].forEach(function(p) {
                    $prodi.append('<option value="'+p+'">'+p+'</option>');
                });
                $('#prodi-wrapper').removeClass('d-none');
            } else {
                $('#prodi-wrapper').addClass('d-none');
            }
        });
      });
    </script>
    @endpush
@endsection
