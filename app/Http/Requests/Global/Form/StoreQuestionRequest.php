<?php

namespace App\Http\Requests\Global\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @method bool hasFile(string $key)
 * @method \Illuminate\Http\UploadedFile|null file(string $key, mixed $default = null)
 */
class StoreQuestionRequest extends FormRequest
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
      'questions.*.m_form_id' => ['required', 'uuid', Rule::exists('m_form', 'id')],
      'questions.*.sequence' => ['required', 'integer'],
      'questions.*.question' => ['required', 'string'],
      'questions.*.type' => ['required', 'string', Rule::in(['text', 'checkbox', 'option'])],
      'questions.*.is_required' => ['nullable', 'boolean'],
      'questions.*.options' => ['required', 'array'],
      'questions.*.options.*.sequence' => ['required', 'integer'],
      'questions.*.options.*.label' => ['required', 'string'],
      'questions.*.options.*.point' => ['required', 'integer'],
    ];
  }

  public function messages(): array
  {
    return [
      'questions.required' => 'Pertanyaan wajib diisi.',
      'questions.array' => 'Format pertanyaan tidak valid.',
      'questions.*.m_form_id.required' => 'ID form wajib diisi.',
      'questions.*.m_form_id.uuid' => 'Format ID form tidak valid.',
      'questions.*.sequence.required' => 'Urutan pertanyaan wajib diisi.',
      'questions.*.sequence.integer' => 'Urutan pertanyaan harus berupa angka.',
      'questions.*.question.required' => 'Teks pertanyaan wajib diisi.',
      'questions.*.question.string' => 'Teks pertanyaan harus berupa string.',
      'questions.*.type.required' => 'Tipe pertanyaan wajib diisi.',
      'questions.*.type.string' => 'Tipe pertanyaan harus berupa string.',
      'questions.*.type.in' => 'Tipe pertanyaan tidak valid.',
      'questions.*.is_required.boolean' => 'Field is_required harus berupa boolean.',
      'questions.*.options.required' => 'Opsi jawaban wajib diisi.',
      'questions.*.options.array' => 'Format opsi jawaban tidak valid.',
      'questions.*.options.*.sequence.required' => 'Urutan opsi jawaban wajib diisi.',
      'questions.*.options.*.sequence.integer' => 'Urutan opsi jawaban harus berupa angka.',
      'questions.*.options.*.label.required' => 'Label opsi jawaban wajib diisi.',
      'questions.*.options.*.label.string' => 'Label opsi jawaban harus berupa string.',
      'questions.*.options.*.point.required' => 'Poin opsi jawaban wajib diisi.',
      'questions.*.options.*.point.integer' => 'Poin opsi jawaban harus berupa angka.',
    ];
  }
}
