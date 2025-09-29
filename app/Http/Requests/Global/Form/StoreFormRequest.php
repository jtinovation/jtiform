<?php

namespace App\Http\Requests\Global\Form;

use App\Enums\FormRespondentTypeEnum;
use App\Enums\FormTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @method bool hasFile(string $key)
 * @method \Illuminate\Http\UploadedFile|null file(string $key, mixed $default = null)
 * @method mixed input(string $key = null, mixed $default = null)
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
      'type'        => ['required', Rule::in(FormTypeEnum::toArray())],
      'title'       => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'start_at'    => ['required', 'date'],
      'end_at'      => ['required', 'date', 'after_or_equal:start_at'],
      'cover'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // KB
      'responden_type' => ['required', Rule::in(FormRespondentTypeEnum::toArray())],
      'major_id' => [
        'nullable',
        'string',
        'uuid'
      ],
      'study_program_id' => [
        'nullable',
        'string',
        'uuid'
      ],
      'respondent_ids' => [
        'nullable',
        'array'
      ],
      'respondent_ids.*' => [
        'string',
        'uuid'
      ]
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
      'responden_type.required' => 'Tipe responden wajib diisi.',
      'responden_type.in'      => 'Tipe responden tidak valid.',
      'major_id.string'        => 'Jurusan tidak valid.',
      'major_id.uuid'          => 'Jurusan tidak valid.',
      'study_program_id.string'   => 'Program studi tidak valid.',
      'study_program_id.uuid'     => 'Program studi tidak valid.',
      'respondent_ids.array'      => 'Daftar responden tidak valid.',
      'respondent_ids.*.string'   => 'Responden tidak valid.',
      'respondent_ids.*.uuid'     => 'Responden tidak valid.',
    ];
  }
}
