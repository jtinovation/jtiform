<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    // 🔹 Tampilkan daftar form
    public function listForm()
    {
        $forms = Form::all();
        return view('form.list', compact('forms'));
    }

    // 🔹 Tampilkan halaman tambah form
    public function tambahForm()
    {
        return view('form.tambah');
    }

    // 🔹 Simpan form baru
    public function simpanForm(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        Form::create($request->all());

        return redirect()->route('form.list')
                         ->with('success', 'Form berhasil dibuat.');
    }

    // 🔹 Edit form
    public function editForm(Form $form)
    {
        return view('form.edit', compact('form'));
    }

    // 🔹 Update form
    public function updateForm(Request $request, Form $form)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $form->update($request->all());

        return redirect()->route('form.list')
                         ->with('success', 'Form berhasil diperbarui.');
    }

    // 🔹 Hapus form
    public function hapusForm(Form $form)
    {
        $form->delete();

        return redirect()->route('form.list')
                         ->with('success', 'Form berhasil dihapus.');
    }
}
