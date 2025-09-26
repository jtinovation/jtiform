<?php

namespace App\Http\Controllers\Global\Form;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Form\StoreFormRequest;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Question;

class FormController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('search');
    $forms = Form::query()
      ->withTrashed()
      ->when($search, function ($query, $search) {
        return $query->where(function ($q) use ($search) {
          $q->where('title', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
        });
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->withQueryString();

    return view('content.form.index', compact('forms'));
  }

  public function create()
  {
    return view('content.form.create');
  }

  public function store(StoreFormRequest $request)
  {
    $validated = $request->validated();

    if ($request->hasFile('cover')) {
      $validated['cover_file'] = FileHelper::storeFile($request->file('cover'), '/form');
      $validated['cover_path'] = '/form';
    }

    Form::create([
      'code' => $validated['code'],
      'type' => $validated['type'],
      'title' => $validated['title'],
      'description' => $validated['description'] ?? null,
      'start_at' => $validated['start_at'],
      'end_at' => $validated['end_at'],
      'cover_path' => $validated['cover_path'] ?? null,
      'cover_file' => $validated['cover_file'] ?? null,
    ]);

    return redirect()->route('form.index')->with('success', 'Form berhasil dibuat.');
  }

  public function show($id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    $questions = Question::where('m_form_id', $id)
      ->with('options')
      ->orderBy('sequence')
      ->get();
    return view('content.form.show', compact('form', 'questions'));
  }

  public function edit($id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    return view('content.form.edit', compact('form'));
  }

  public function update(StoreFormRequest $request, $id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    $validated = $request->validated();

    if ($request->hasFile('cover')) {
      // Hapus file lama jika ada
      if ($form->cover_file && $form->cover_path) {
        FileHelper::deleteFile($form->cover_file, $form->cover_path);
      }
      $validated['cover_file'] = FileHelper::storeFile($request->file('cover'), '/form');
      $validated['cover_path'] = '/form';
    }

    $form->update([
      'code' => $validated['code'],
      'type' => $validated['type'],
      'title' => $validated['title'],
      'description' => $validated['description'] ?? null,
      'start_at' => $validated['start_at'],
      'end_at' => $validated['end_at'],
      'cover_path' => $validated['cover_path'] ?? $form->cover_path,
      'cover_file' => $validated['cover_file'] ?? $form->cover_file,
    ]);

    return redirect()->route('form.index')->with('success', 'Form berhasil diperbarui.');
  }

  public function restore($id)
  {
    $form = Form::withTrashed()->findOrFail($id);

    if ($form->trashed()) {
      $form->restore();
      return redirect()->route('form.index')->with('success', 'Form berhasil dipulihkan.');
    }

    return redirect()->route('form.index')->with('info', 'Form tidak dalam keadaan terhapus.');
  }

  public function delete($id)
  {
    $form = Form::withTrashed()->findOrFail($id);

    if ($form->trashed()) {
      $form->forceDelete();
    } else {
      $form->delete();
    }

    return response()->json(['success' => true, 'message' => 'Form berhasil dihapus.']);
  }
}
