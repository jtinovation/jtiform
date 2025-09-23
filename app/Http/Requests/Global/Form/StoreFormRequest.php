<?php

namespace App\Http\Requests\Global\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @method bool hasFile(string $key)
 * @method \Illuminate\Http\UploadedFile|null file(string $key, mixed $default = null)
 */
class StoreFormRequest extends FormRequest
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
      'code'        => ['required', Rule::unique('m_form', 'code')],
      'type'        => ['required', Rule::in(['questionnaire', 'survey'])],
      'title'       => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'start_at'    => ['required', 'date'],
      'end_at'      => ['required', 'date', 'after_or_equal:start_at'],
      'cover'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // KB
    ];
  }

  public function messages(): array
  {
    return [
      'code.required'          => 'Kode wajib diisi.',
      'code.unique'            => 'Kode sudah digunakan.',
      'type.required'          => 'Tipe wajib diisi.',
      'type.in'                => 'Tipe tidak valid.',
      'title.required'         => 'Judul wajib diisi.',
      'title.max'              => 'Judul maksimal 255 karakter.',
      'start_at.required'      => 'Tanggal mulai wajib diisi.',
      'start_at.date'          => 'Tanggal mulai tidak valid.',
      'end_at.required'        => 'Tanggal berakhir wajib diisi.',
      'end_at.date'            => 'Tanggal berakhir tidak valid.',
      'end_at.after_or_equal'  => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
      'cover.image'            => 'File harus berupa gambar.',
      'cover.mimes'            => 'Format gambar harus jpeg, png, jpg, atau gif.',
      'cover.max'              => 'Ukuran gambar maksimal 2MB.',
    ];
  }
}
