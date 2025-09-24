@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Pertanyaan')

@section('content')
<div class="row">
  <div class="col-xxl">
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="mb-0">Edit Pertanyaan</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('question.update', [$form->id, $question->id]) }}" method="POST">
          @csrf
          @method('PUT')

          {{-- Pertanyaan --}}
          <div class="row mb-4">
            <label class="col-sm-2 col-form-label">Pertanyaan</label>
            <div class="col-sm-10">
              <input type="text" name="question"
                     class="form-control @error('question') is-invalid @enderror"
                     value="{{ old('question', $question->question) }}" required>
              @error('question')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Urutan --}}
          <div class="row mb-4">
            <label class="col-sm-2 col-form-label">Urutan</label>
            <div class="col-sm-10">
              <input type="number" name="sequence"
                     class="form-control @error('sequence') is-invalid @enderror"
                     value="{{ old('sequence', $question->sequence ?? 1) }}" min="1" required>
              @error('sequence')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Bagian Jawaban --}}
          <div class="border p-3 rounded mt-3 bg-white">
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Tipe Jawaban</label>
              <div class="col-sm-10">
                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                  <option value="">-- Pilih Tipe --</option>
                  <option value="text" {{ old('type', $question->type)=='text' ? 'selected' : '' }}>Text</option>
                  <option value="option" {{ old('type', $question->type)=='option' ? 'selected' : '' }}>Option</option>
                  <option value="checkbox" {{ old('type', $question->type)=='checkbox' ? 'selected' : '' }}>Checkbox</option>
                </select>
                @error('type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Wajib Diisi?</label>
              <div class="col-sm-10">
                <input type="hidden" name="is_required" value="0">
                <input type="checkbox" name="is_required" value="1"
                  {{ old('is_required', $question->is_required) ? 'checked' : '' }}>
              </div>
            </div>

            {{-- Keterangan Opsi --}}
            <div class="row mb-3" id="keterangan-wrapper" style="display:none;">
              <label class="col-sm-2 col-form-label">Keterangan</label>
              <div class="col-sm-10" id="options-container"></div>
            </div>
          </div>

          {{-- Tombol --}}
          <div class="row justify-content-end mt-4">
            <div class="col-sm-10">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="{{ route('forms.questions.index', $form->id) }}" class="btn btn-secondary ms-2">Batal</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const typeSelect = document.getElementById('type');
  const wrapperKeterangan = document.getElementById('keterangan-wrapper');
  const container = document.getElementById('options-container');

  const oldOptions = @json(old('options', $question->options->pluck('answer')->toArray()));

  function renderAnswerOptions(type) {
    container.innerHTML = '';
    wrapperKeterangan.style.display = 'none';

    if (type === 'text') {
      wrapperKeterangan.style.display = 'block';
      container.innerHTML = `<input type="text" class="form-control" placeholder="Jawaban akan diisi user" disabled>`;
      return;
    }

    if (type === 'option' || type === 'checkbox') {
      wrapperKeterangan.style.display = 'block';

      const optionWrapper = document.createElement('div');
      optionWrapper.className = 'option-wrapper';

      const addOptionBtn = document.createElement('button');
      addOptionBtn.type = 'button';
      addOptionBtn.className = 'btn btn-sm btn-secondary mt-2';
      addOptionBtn.innerText = 'Tambah Opsi Jawaban';
      addOptionBtn.addEventListener('click', () => addOptionRow(optionWrapper));

      container.appendChild(optionWrapper);
      container.appendChild(addOptionBtn);

      if (oldOptions.length > 0) {
        oldOptions.forEach(opt => addOptionRow(optionWrapper, opt));
      } else {
        addOptionRow(optionWrapper);
      }
    }
  }

  function addOptionRow(wrapper, value = '') {
    const optionGroup = document.createElement('div');
    optionGroup.className = 'input-group mb-2';
    optionGroup.innerHTML = `
      <input type="text" name="options[]" class="form-control" placeholder="Isi keterangan" value="${value}">
      <button type="button" class="btn btn-danger">Hapus</button>
    `;
    optionGroup.querySelector('button').addEventListener('click', () => optionGroup.remove());
    wrapper.appendChild(optionGroup);
  }

  typeSelect.addEventListener('change', () => renderAnswerOptions(typeSelect.value));
  renderAnswerOptions(typeSelect.value); // initial load
});
</script>
@endsection
