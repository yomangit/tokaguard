<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class FileHelper
{
    public static function compressAndStore($file, $folder, $width = 800, $quality = 75)
    {
        $filename  = time() . '-' . $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $relativePath = $folder . '/' . $filename;

        $manager = new ImageManager(new Driver());

        // Tentukan disk default (cek apakah storage:link aktif)
        $disk = is_link(public_path('storage')) ? 'public' : 'uploads';

        // Jika gambar
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $image = $manager->read($file->getRealPath())
                ->scale(width: $width)
                ->encodeByExtension($extension, quality: $quality);

            Storage::disk($disk)->put($relativePath, (string) $image);
            // Copy ke public/storage manual
            copy(storage_path('app/public/' . $relativePath), public_path('storage/' . $relativePath));
        } else {
            $relativePath = $file->storeAs($folder, $filename, $disk);
        }

        // Return URL langsung (bukan path internal)
        return Storage::disk($disk)->url($relativePath);
    }
}
