<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FormController extends Controller
{
    // 🔹 Tampilkan daftar form
    public function listForm()
    {
        $forms = Form::all();
        return view('content.form-layout.ListForm', compact('forms'));
    }

    // 🔹 Tampilkan halaman tambah form
    public function tambahForm()
    {
        return view('content.form-layout.TambahForm');
    }

    // 🔹 Simpan form baru
    public function simpanForm(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        // simpan form
        Form::create($request->all());

        // redirect ke halaman daftar form aktif
        return redirect('/form')->with('success', 'Form berhasil dibuat.');
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        $form->update($request->all());

        return redirect('/form')->with('success', 'Form berhasil diperbarui.');
    }

    // 🔹 Hapus form
    public function hapusForm(Form $form)
    {
        $form->delete();

        return redirect('/form')->with('success', 'Form berhasil dihapus.');
    }

    // 🔹 Tampilkan form aktif berdasarkan tanggal sekarang
    public function showActiveForm()
    {
        $now = Carbon::now();

        $forms = Form::where(function ($q) use ($now) {
                        $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
                    })
                    ->get();

        return view('content.form.form-active', compact('forms'));
    }

    // 🔹 Tampilkan semua form
    public function showForm()
    {
        $forms = Form::get();
        return view('content.form.form-master', compact('forms'));
    }

    // 🔹 Tampilkan pertanyaan berdasarkan form
    public function showQuestionList($id)
    {
        $questions = Question::where('m_form_id', $id)
                             ->orderBy('sequence', 'asc')
                             ->get();

        return view('content.form.questions', compact('questions'));
    }

    public function checkTable()
    {
        return view('content.user-interface.ui-buttons');
    }
}
