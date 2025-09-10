@extends('layouts/contentNavbarLayout')

@section('title', 'Management Event | Edit Event')

@section('page-style')
    <style>
        .ck-content {
            min-height: 300px;
        }
    </style>
@endsection

@section('page-script')
    @vite(['resources/assets/js/create-event.js'])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-6">
                <form action="{{ route('admin.event.update', $event->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body pt-0">
                        <div class="row mt-1 g-5">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('title') is-invalid @enderror" type="text"
                                        id="title" name="title" value="{{ $event->title }}" autofocus
                                        placeholder="Konser Meriah" />
                                    <label for="title">Judul Event</label>
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('type') is-invalid @enderror" type="text"
                                        id="type" name="type" value="{{ $event->type }}" autofocus
                                        placeholder="Konser" />
                                    <label for="type">Tipe Event</label>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('venue') is-invalid @enderror" type="text"
                                        id="venue" name="venue" value="{{ $event->venue }}" autofocus
                                        placeholder="Jakarta, Indonesia" />
                                    <label for="venue">Venue Event</label>
                                    @error('venue')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('venue_maps_url') is-invalid @enderror" type="url"
                                        id="venue_maps_url" name="venue_maps_url" value="{{ $event->venue_maps_url }}"
                                        autofocus placeholder="https://maps.google.com/?q=Jakarta,Indonesia" />
                                    <label for="venue_maps_url">URL Venue Maps</label>
                                    @error('venue_maps_url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('schedule') is-invalid @enderror" type="date"
                                        id="schedule" name="schedule" value="{{ $event->schedule }}" autofocus
                                        placeholder="Tanggal Event" />
                                    <label for="schedule">Tanggal Event</label>
                                    @error('schedule')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('total_quota') is-invalid @enderror" type="number"
                                        id="total_quota" name="total_quota" value="{{ $event->total_quota }}" autofocus
                                        placeholder="100" min="1" />
                                    <label for="total_quota">Total Kuota</label>
                                    @error('total_quota')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('start_time') is-invalid @enderror" type="time"
                                        id="start_time" name="start_time"
                                        value="{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}" autofocus
                                        placeholder="Waktu start" />
                                    <label for="start_time">Waktu Start Event</label>
                                    @error('start_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('end_time') is-invalid @enderror" type="time"
                                        id="end_time" name="end_time"
                                        value="{{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}" autofocus
                                        placeholder="Waktu end" />
                                    <label for="end_time">Waktu End Event</label>
                                    @error('end_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('cp_email') is-invalid @enderror" type="email"
                                        id="cp_email" name="cp_email" value="{{ $event->cp_email }}" autofocus
                                        placeholder="Email Contact Person" />
                                    <label for="cp_email">Email Contact Person</label>
                                    @error('cp_email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('cp_phone') is-invalid @enderror" type="tel"
                                        id="cp_phone" name="cp_phone" value="{{ $event->cp_phone }}" autofocus
                                        placeholder="Nomor Telepon Contact Person" />
                                    <label for="cp_phone">Nomor Telepon Contact Person</label>
                                    @error('cp_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="description">Deskripsi Event</label>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <textarea rows="4" id="description" class="h-25 form-control @error('description') is-invalid @enderror"
                                    name="description">{{ $event->description }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label for="image" class="form-label">Gambar Thumbnail</label>
                                <a href="{{ $event->thumbnailUrl() }}" target="_blank">Lihat Gambar</a>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image" accept=".png, .jpg, .jpeg" />
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="image" class="form-label">Gambar Banner</label>
                                <a href="{{ $event->bannerUrl() }}" target="_blank">Lihat Gambar</a>
                                <input type="file" class="form-control @error('banner') is-invalid @enderror"
                                    id="banner" name="banner" accept=".png, .jpg, .jpeg" />
                                @error('banner')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12" id="gallery-container">
                                <label for="gallery" class="form-label">Gallery Event</label>
                                <div class="input-group">
                                    <input type="file" class="form-control @error('gallery.*') is-invalid @enderror"
                                        id="gallery" name="gallery[]" accept=".png, .jpg, .jpeg" multiple />
                                    @error('gallery.*')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span class="input-group-text cursor-pointer" id="add-gallery">
                                        <i class="ri-add-line ri-20px"></i>
                                    </span>
                                </div>
                            </div>

                            <template id="gallery-template">
                                <div class="col-md-12 gallery-item">
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="gallery" name="gallery[]"
                                            accept=".png, .jpg, .jpeg" multiple />
                                        <span class="input-group-text cursor-pointer text-danger remove-gallery">
                                            <i class="ri-delete-bin-5-line ri-20px"></i>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button type="submit" class="btn btn-primary mt-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
