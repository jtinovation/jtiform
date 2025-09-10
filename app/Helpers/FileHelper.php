<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
  public static function get(string|null $path, string|null $filename = '')
  {
    if ($path === null && $filename === null) return null;

    $fullPath = $path . '/' . $filename;

    if (empty($filename)) {
      $fullPath = $path;
    }

    return config('filesystems.disks.public.url') . $fullPath;
  }

  public static function storeFile(UploadedFile $file, string $path, ?string $filename = ''): string
  {
    if (empty($filename)) {
      $filename = Str::random() . '.' . $file->getClientOriginalExtension();
    }

    $file->storeAs($path, $filename);

    return $filename;
  }

  public static function deleteFile(string $path, ?string $filename): string
  {
    if ($filename === null) return '';

    $fullPath = $path . '/' . $filename;

    if (Storage::disk('public')->exists($fullPath)) {
      Storage::disk('public')->move($fullPath, 'trash/' . $fullPath);

      return $filename;
    }

    return '';
  }
}
