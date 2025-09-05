<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File; // ganti Storage jadi File
use Intervention\Image\Drivers\Gd\Driver;

class FileHelper
{
    public static function compressAndStore($file, $folder, $width = 800, $quality = 75)
    {
        $filename  = time() . '-' . $file->getClientOriginalName(); // supaya unik
        $extension = strtolower($file->getClientOriginalExtension());
        $folderPath = public_path('uploads/' . $folder);

        // Buat folder jika belum ada
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $path = 'uploads/' . $folder . '/' . $filename;

        $manager = new ImageManager(new Driver());

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            // Resize + kompres
            $image = $manager->read($file->getRealPath())
                ->scale(width: $width)
                ->encodeByExtension($extension, quality: $quality);

            file_put_contents(public_path($path), (string) $image);
        } else {
            // kalau bukan gambar (misal PDF), simpan langsung
            $file->move($folderPath, $filename);
        }

        return $path; // simpan path relatif untuk database
    }
}
