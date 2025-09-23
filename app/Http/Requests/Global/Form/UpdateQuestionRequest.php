<?php

namespace App\Http\Requests\Global\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'questions' => ['required', 'array'],
      'questions.*.id' => ['nullable', 'uuid', Rule::exists('m_question', 'id')],
      'questions.*.m_form_id' => ['required', 'uuid', Rule::exists('m_form', 'id')],
      'questions.*.sequence' => ['required', 'integer', 'min:1'],
      'questions.*.question' => ['required', 'string'],
      'questions.*.type' => ['required', Rule::in(['text', 'checkbox', 'option'])],
      'questions.*.is_required' => ['nullable', 'boolean'],

      // options hanya wajib jika tipe pilihan
      'questions.*.options' => ['nullable', 'array'],
      'questions.*.options.*.id' => ['nullable', 'uuid', Rule::exists('m_question_option', 'id')],
      'questions.*.options.*.sequence' => ['required_with:questions.*.options', 'integer', 'min:1'],
      'questions.*.options.*.label' => ['required_with:questions.*.options', 'string'],
      'questions.*.options.*.point' => ['required_with:questions.*.options', 'integer'],

      // bins untuk delete
      'removed_question_ids' => ['nullable', 'array'],
      'removed_question_ids.*' => ['uuid', Rule::exists('m_question', 'id')],
      'removed_option_ids' => ['nullable', 'array'],
      'removed_option_ids.*' => ['uuid', Rule::exists('m_question_option', 'id')],
    ];
  }

  public function messages(): array
  {
    return [
      'questions.required' => 'Pertanyaan wajib diisi.',
      'questions.array' => 'Format pertanyaan tidak valid.',
      'questions.*.id.uuid' => 'Format ID pertanyaan tidak valid.',
      'questions.*.id.exists' => 'ID pertanyaan tidak ditemukan.',
      'questions.*.m_form_id.required' => 'ID form wajib diisi.',
      'questions.*.m_form_id.uuid' => 'Format ID form tidak valid.',
      'questions.*.m_form_id.exists' => 'ID form tidak ditemukan.',
      'questions.*.sequence.required' => 'Urutan pertanyaan wajib diisi.',
      'questions.*.sequence.integer' => 'Urutan pertanyaan harus berupa angka.',
      'questions.*.sequence.min' => 'Urutan pertanyaan minimal 1.',
      'questions.*.question.required' => 'Teks pertanyaan wajib diisi.',
      'questions.*.question.string' => 'Teks pertanyaan harus berupa string.',
      'questions.*.type.required' => 'Tipe pertanyaan wajib diisi.',
      'questions.*.type.in' => 'Tipe pertanyaan tidak valid.',
      'questions.*.is_required.boolean' => 'Field is_required harus berupa boolean.',

      'questions.*.options.array' => 'Format opsi jawaban tidak valid.',
      'questions.*.options.*.id.uuid' => 'Format ID opsi jawaban tidak valid.',
      'questions.*.options.*.id.exists' => 'ID opsi jawaban tidak ditemukan.',
      'questions.*.options.*.sequence.required_with' => 'Urutan opsi jawaban wajib diisi jika ada opsi.',
      'questions.*.options.*.sequence.integer' => 'Urutan opsi jawaban harus berupa angka.',
      'questions.*.options.*.sequence.min' => 'Urutan opsi jawaban minimal 1.',
      'questions.*.options.*.label.required_with' => 'Label opsi jawaban wajib diisi jika ada opsi.',
      'questions.*.options.*.label.string' => 'Label opsi jawaban harus berupa string.',
      'questions.*.options.*.point.required_with' => 'Poin opsi jawaban wajib diisi jika ada opsi.',
      'questions.*.options.*.point.integer' => 'Poin opsi jawaban harus berupa angka.',

      'removed_question_ids.array' => 'Format removed_question_ids tidak valid.',
      'removed_question_ids.*.uuid' => 'Format ID pertanyaan yang dihapus tidak valid.',
      'removed_question_ids.*.exists' => 'ID pertanyaan yang dihapus tidak ditemukan.',
      'removed_option_ids.array' => 'Format removed_option_ids tidak valid.',
      'removed_option_ids.*.uuid' => 'Format ID opsi jawaban yang dihapus tidak valid.',
      'removed_option_ids.*.exists' => 'ID opsi jawaban yang dihapus tidak ditemukan.',
    ];
  }
}
