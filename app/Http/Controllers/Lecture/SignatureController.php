<?php

namespace App\Http\Controllers\Lecture;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
  public function index()
  {
    return view('content.lecture.signature.index');
  }

  public function store(Request $request)
  {
    $user = ApiHelper::getMe(Auth::user()->token);
    // 1) Jika datang dari Signature Pad, ubah dataURL -> UploadedFile agar bisa divalidasi & di-attach
    if (!$request->hasFile('signature') && $request->filled('signature_data')) {
      $uploaded = $this->dataUrlToUploadedFile($request->input('signature_data'));
      // injeksikan ke $request->files supaya rules 'file' bekerja
      $request['signature'] = $uploaded;
    }

    // 2) Validasi BE
    $validated = $request->validate([
      // max:2048 => 2MB
      'signature' => ['required', 'file', 'mimetypes:image/png,image/jpeg,image/gif,image/svg+xml', 'max:2048'],
    ]);

    $file   = $validated['signature'];
    $token  = Auth::user()->token;            // sesuaikan field token-mu
    $extId  = Arr::get($user, 'employee_detail.id');      // id external di Super App
    $base   = config('app.super_app_url');
    $url    = rtrim($base, '/') . "/employees/{$extId}/sign"; // sesuaikan path endpoint Go

    // 3) Forward ke Go sebagai multipart: field harus 'sign'
    try {
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
      ])
        ->attach('sign', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
        ->post($url);

      if ($response->failed()) {
        Log::warning('Forward sign failed', [
          'status' => $response->status(),
          'body'   => $response->body(),
        ]);
        return back()->withErrors(['signature' => 'Gagal mengunggah ke Super App.'])->withInput();
      }

      return back()->with('success', 'Tanda tangan berhasil disimpan.');
    } finally {
      // 4) Bersihkan file temp jika berasal dari dataURL
      if (isset($uploaded) && $uploaded instanceof UploadedFile) {
        @unlink($uploaded->getRealPath());
      }
    }
  }

  /**
   * Mengubah dataURL (data:image/...;base64,xxxx) menjadi UploadedFile.
   */
  private function dataUrlToUploadedFile(string $dataUrl): UploadedFile
  {
    if (!preg_match('/^data:(.*?);base64,(.*)$/', $dataUrl, $m)) {
      throw ValidationException::withMessages(['signature' => 'Format tanda tangan tidak valid.']);
    }
    $mime = $m[1];
    $data = base64_decode($m[2], true);
    if ($data === false) {
      throw ValidationException::withMessages(['signature' => 'Data tanda tangan rusak.']);
    }

    // tentukan ekstensi dari mime
    $extMap = [
      'image/png'      => 'png',
      'image/jpeg'     => 'jpg',
      'image/gif'      => 'gif',
      'image/svg+xml'  => 'svg',
    ];
    $ext = $extMap[$mime] ?? 'png';

    $tmpDir = storage_path('app/tmp');
    if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);

    $filename = 'signature-' . Str::uuid() . '.' . $ext;
    $path = $tmpDir . DIRECTORY_SEPARATOR . $filename;
    file_put_contents($path, $data);

    // UploadedFile($path, $originalName, $mimeType, $error, $test)
    return new UploadedFile($path, $filename, $mime, null, true);
  }
}
