<?php

namespace App\Http\Requests\Global\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CloneFormRequest extends FormRequest
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
      'code'        => [
        'required',
        'string',
        'max:255',
        Rule::unique('m_form', 'code')->whereNull('deleted_at'),
      ],
      'title'       => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'start_at'    => ['required', 'date'],
      'end_at'      => ['required', 'date', 'after_or_equal:start_at'],
    ];
  }

  public function messages(): array
  {
    return [
      'code.unique' => 'Kode form sudah digunakan.',
      'end_at.after_or_equal' => 'End At harus >= Start At.',
    ];
  }
}
