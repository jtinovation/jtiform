@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Pertanyaan')

@section('content')
<div class="row">
  <div class="col-xxl">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tambah Pertanyaan</h5>
        <button type="button" class="btn btn-success btn-sm" id="add-question-btn">
          + Tambah Pertanyaan
        </button>
      </div>

      <div class="card-body">
        <form action="{{ route('question.store', $form->id) }}" method="POST">
          @csrf

          {{-- Container untuk semua pertanyaan --}}
          <div id="questions-wrapper"></div>

          {{-- Tombol Simpan --}}
          <div class="row justify-content-end mt-4">
            <div class="col-sm-10">
              <button type="submit" class="btn btn-primary">Simpan</button>
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
  const wrapper = document.getElementById('questions-wrapper');
  const addBtn = document.getElementById('add-question-btn');
  let questionIndex = 0;

  // Fungsi untuk membuat form pertanyaan baru
  function addQuestionForm() {
    const index = questionIndex++;
    const questionCard = document.createElement('div');
    questionCard.className = 'border p-3 mb-4 rounded position-relative bg-white';

    questionCard.innerHTML = `
      <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-question"></button>

      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Pertanyaan</label>
        <div class="col-sm-10">
          <input type="text" name="questions[${index}][question]" class="form-control" required>
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Urutan</label>
        <div class="col-sm-10">
          <input type="number" name="questions[${index}][sequence]" class="form-control" min="1" value="1" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-12">
          <button type="button" class="btn btn-info btn-sm" id="show-answer-${index}">
            + Tambah Jawaban
          </button>
        </div>
      </div>

      {{-- Bagian ini awalnya tersembunyi --}}
      <div id="answer-section-${index}" class="border p-3 rounded mt-3" style="display:none; background-color:#ffffff;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <button type="button" class="btn btn-danger btn-sm" id="hide-answer-${index}">Batalkan Jawaban</button>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Tipe Jawaban</label>
          <div class="col-sm-10">
            <select name="questions[${index}][type]" class="form-control question-type" data-index="${index}">
              <option value="">-- Pilih Tipe --</option>
              <option value="text">Text</option>
              <option value="option">Option</option>
              <option value="checkbox">Checkbox</option>
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Wajib Diisi?</label>
          <div class="col-sm-10">
            <input type="hidden" name="questions[${index}][is_required]" value="0">
            <input type="checkbox" name="questions[${index}][is_required]" value="1">
          </div>
        </div>

        {{-- Bagian Keterangan hanya tampil jika tipe = option atau checkbox --}}
        <div class="row mb-3" id="keterangan-wrapper-${index}" style="display:none;">
          <label class="col-sm-2 col-form-label">Keterangan</label>
          <div class="col-sm-10" id="options-container-${index}"></div>
        </div>
      </div>
    `;

    wrapper.appendChild(questionCard);

    // Event untuk hapus form pertanyaan
    questionCard.querySelector('.remove-question').addEventListener('click', () => {
      questionCard.remove();
    });

    // Event untuk tampilkan bagian jawaban
    questionCard.querySelector(`#show-answer-${index}`).addEventListener('click', () => {
      document.getElementById(`answer-section-${index}`).style.display = 'block';
    });

    // Event untuk sembunyikan bagian jawaban
    questionCard.querySelector(`#hide-answer-${index}`).addEventListener('click', () => {
      document.getElementById(`answer-section-${index}`).style.display = 'none';
    });

    // Event ketika user ganti tipe jawaban
    const typeSelect = questionCard.querySelector('.question-type');
    typeSelect.addEventListener('change', () => renderAnswerOptions(typeSelect.value, index));
  }

  // Fungsi render keterangan / jawaban
  function renderAnswerOptions(type, index) {
    const wrapperKeterangan = document.getElementById(`keterangan-wrapper-${index}`);
    const container = document.getElementById(`options-container-${index}`);
    container.innerHTML = '';

    // Default sembunyikan
    wrapperKeterangan.style.display = 'none';

    // Jika type text, cukup tampilkan input text yang disabled
    if (type === 'text') {
      wrapperKeterangan.style.display = 'block';
      container.innerHTML = `<input type="text" class="form-control" placeholder="Jawaban akan diisi user" disabled>`;
      return;
    }

    // Jika type option / checkbox tampilkan dan bisa tambah opsi
    if (type === 'option' || type === 'checkbox') {
      wrapperKeterangan.style.display = 'block';

      const optionWrapper = document.createElement('div');
      optionWrapper.className = 'option-wrapper';

      const addOptionBtn = document.createElement('button');
      addOptionBtn.type = 'button';
      addOptionBtn.className = 'btn btn-sm btn-secondary mt-2';
      addOptionBtn.innerText = 'Tambah Opsi Jawaban';
      addOptionBtn.addEventListener('click', () => addOptionRow(optionWrapper, index));

      container.appendChild(optionWrapper);
      container.appendChild(addOptionBtn);

      // Default tambah 1 opsi
      addOptionRow(optionWrapper, index);
    }
  }

  // Fungsi untuk tambah baris opsi jawaban
  function addOptionRow(wrapper, index) {
    const optionGroup = document.createElement('div');
    optionGroup.className = 'input-group mb-2';
    optionGroup.innerHTML = `
      <input type="text" name="questions[${index}][options][]" class="form-control" placeholder="Isi keterangan">
      <button type="button" class="btn btn-danger">Hapus</button>
    `;
    optionGroup.querySelector('button').addEventListener('click', () => optionGroup.remove());
    wrapper.appendChild(optionGroup);
  }

  addQuestionForm();


  addBtn.addEventListener('click', addQuestionForm);
});
</script>
@endsection
