<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FormController extends Controller
{
  // 🔹 Tampilkan halaman tambah form
  public function tambahForm()
  {
    return view('content.form-layout.TambahForm');
  }
    // 🔹 Tampilkan halaman tambah form
    public function createForm()
    {
        return view('content.form-layout.CreateForm');
    }

  // 🔹 Simpan form baru
  public function simpanForm(Request $request)
  {
    $request->validate([
      'code'        => 'required|string|max:50',
      'type'   => 'required|in:questionnaire,survey',
      'title'       => 'required|string|max:255',
      'description' => 'nullable|string',
      'start_at'    => 'required|date',
      'end_at'      => 'required|date',
      'cover_path'  => 'nullable|string|max:255',
      'cover_file'  => 'nullable|string|max:255',
    ], [
      'code.required'       => 'Code is required.',
      'code.max'            => 'Code cannot exceed 50 characters.',
      'type.required'  => 'Form type is required.',
      'type.in'        => 'Form type must be either questionnaire or survey.',
      'title.required'      => 'Title is required.',
      'title.max'           => 'Title cannot exceed 255 characters.',
      'description.string'  => 'Description must be a string.',
      'start_at.required'   => 'Start date is required.',
      'start_at.date'       => 'Start date must be a valid date.',
      'end_at.required'     => 'End date is required.',
      'end_at.date'         => 'End date must be a valid date.',
      'cover_path.max'      => 'Cover path cannot exceed 255 characters.',
      'cover_file.max'      => 'Cover file cannot exceed 255 characters.',
    ]);
 public function storeForm(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:50',
            'form_type'   => 'required|in:questionnaire,survey',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'required|date',
            'end_at'      => 'required|date',
            'cover_path'  => 'nullable|string|max:255',
            'cover_file'  => 'nullable|string|max:255',
        ], [
            'code.required'       => 'Code is required.',
            'code.max'            => 'Code cannot exceed 50 characters.',
            'form_type.required'  => 'Form type is required.',
            'form_type.in'        => 'Form type must be either questionnaire or survey.',
            'title.required'      => 'Title is required.',
            'title.max'           => 'Title cannot exceed 255 characters.',
            'description.string'  => 'Description must be a string.',
            'start_at.required'   => 'Start date is required.',
            'start_at.date'       => 'Start date must be a valid date.',
            'end_at.required'     => 'End date is required.',
            'end_at.date'         => 'End date must be a valid date.',
            'cover_path.max'      => 'Cover path cannot exceed 255 characters.',
            'cover_file.max'      => 'Cover file cannot exceed 255 characters.',
        ]);

    $now     = Carbon::now();
    $startAt = Carbon::parse($request->start_at);
    $endAt   = Carbon::parse($request->end_at);


    if ($startAt->lt($now->copy()->startOfDay())) {
      return back()->withErrors(['start_at' => 'Start date cannot be before today.'])->withInput();
    }

    if ($endAt->lt($now->copy()->startOfDay())) {
      return back()->withErrors(['end_at' => 'End date cannot be before today.'])->withInput();
    }

    if ($endAt->lte($startAt)) {
      return back()->withErrors(['end_at' => 'End date must be after the start date.'])->withInput();
    }


    $isActive = (
      $startAt->toDateString() <= $now->toDateString() &&
      $endAt->toDateString()   >= $now->toDateString()
    ) ? 1 : 0;


    $form = new Form();
    $form->code        = $request->code;
    $form->type   = $request->type;
    $form->title       = $request->title;
    $form->description = $request->description;
    $form->start_at    = $startAt->format('Y-m-d H:i:s');
    $form->end_at      = $endAt->format('Y-m-d H:i:s');
    $form->cover_path  = $request->cover_path;
    $form->cover_file  = $request->cover_file;
    $form->is_active   = $isActive;
    $form->save();

    return redirect('/form')->with('success', 'Form successfully created.');
  }

  // 🔹 Edit form
  public function editForm(Form $form)
  {
    return view('content.form-layout.EditForm', compact('form'));
  }


  // 🔹 Update form
  public function updateForm(Request $request, Form $form)
  {
    $request->validate([
      'code'        => 'required|string|max:50',
      'type'   => 'required|in:questionnaire,survey',
      'title'       => 'required|string|max:255',
      'description' => 'nullable|string',
      'start_at'    => 'required|date',
      'end_at'      => 'required|date',
      'cover_path'  => 'nullable|string|max:255',
      'cover_file'  => 'nullable|string|max:255',
    ], [
      'code.required'       => 'Code is required.',
      'code.max'            => 'Code cannot exceed 50 characters.',
      'type.required'  => 'Form type is required.',
      'type.in'        => 'Form type must be either questionnaire or survey.',
      'title.required'      => 'Title is required.',
      'title.max'           => 'Title cannot exceed 255 characters.',
      'description.string'  => 'Description must be a string.',
      'start_at.required'   => 'Start date is required.',
      'start_at.date'       => 'Start date must be a valid date.',
      'end_at.required'     => 'End date is required.',
      'end_at.date'         => 'End date must be a valid date.',
      'cover_path.max'      => 'Cover path cannot exceed 255 characters.',
      'cover_file.max'      => 'Cover file cannot exceed 255 characters.',
    ]);

    $now     = Carbon::now();
    $startAt = Carbon::parse($request->start_at);
    $endAt   = Carbon::parse($request->end_at);


    if ($endAt->lte($startAt)) {
      return back()->withErrors(['end_at' => 'End date must be after the start date.'])->withInput();
    }


    $isActive = (
      $startAt->toDateString() <= $now->toDateString() &&
      $endAt->toDateString()   >= $now->toDateString()
    ) ? 1 : 0;


    $form->code        = $request->code;
    $form->type   = $request->type;
    $form->title       = $request->title;
    $form->description = $request->description;
    $form->start_at    = $startAt->format('Y-m-d H:i:s');
    $form->end_at      = $endAt->format('Y-m-d H:i:s');
    $form->cover_path  = $request->cover_path;
    $form->cover_file  = $request->cover_file;
    $form->is_active   = $isActive;
    $form->save();

    return redirect('/form')->with('success', 'Form successfully updated.');
  }
  // 🔹 Hapus form
  public function hapusForm($id)
  {
public function deleteForm($id)
{
    try {

      $form = Form::findOrFail($id);
      $form->delete();

      return redirect('/form')->with('success', 'Form berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Form tidak ditemukan atau gagal dihapus.');
    }
  }

  // 🔹 Tampilkan form aktif berdasarkan tanggal sekarang
  public function showActiveForm(Request $request)
  {
    $search = $request->query('search-input');

    $now = Carbon::now();

    $query = Form::query();

    $query->where('is_active', true);

    $query->where(function ($q) use ($now) {
      $q->whereNull('start_at')->orWhereDate('start_at', '<=', $now->toDateString());
    })
      ->where(function ($q) use ($now) {
        $q->whereNull('end_at')->orWhereDate('end_at', '>=', $now->toDateString());
      });

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")
          ->orWhere('type', 'like', "%{$search}%");
      });
    }

    $forms = $query->latest('end_at')->paginate(10)->withQueryString();

    return view('content.form.form-active', compact('forms'));
  }

  // 🔹 Tampilkan semua form
  public function showForm(Request $request)
  {
    $search = $request->query('search-input');

    $query = Form::query();

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")
          ->orWhere('type', 'like', "%{$search}%");
      });
    }

    $forms = $query->paginate(10)->withQueryString();

    return view('content.form.form-master', compact('forms'));
  }

  // 🔹 Tampilkan pertanyaan berdasarkan form
  public function showQuestionList(Request $request, $id)
  {
    $search = $request->query('search-input');

    $query = Question::query();

    $query->where('m_form_id', $id)
      ->orderBy('sequence', 'asc');

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('question', 'like', "%{$search}%")
          ->orWhere('type', 'like', "%{$search}%");
      });
    }

    $questions = $query->paginate(10)->withQueryString();

    return view('content.form.questions', compact('questions'));
  }

  public function checkFile()
  {
    return view('content.user-interface.ui-pagination-breadcrumbs');
  }
}
  }
}
