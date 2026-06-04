<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUploadHelper
{
    /**
     * Upload file ke disk public.
     * @param UploadedFile $file
     * @param string $folder — subfolder di storage (contoh: 'posts/banners')
     * @return string — path relatif file yang tersimpan
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');
        return $path;
    }

    /**
     * Hapus file dari disk public.
     * @param string|null $path — path relatif file
     */
    public function deleteFile(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Generate full URL dari path relatif.
     * @param string|null $path
     * @return string|null
     */
    public function fileUrl(?string $path): ?string
    {
        if (!$path) return null;
        return asset('storage/' . $path);
    }
}
