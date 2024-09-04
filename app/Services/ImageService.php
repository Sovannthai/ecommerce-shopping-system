<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class ImageService
{
    public function uploadImage(UploadedFile $image, $directory = 'uploads/all_photo'): string
    {
        $extension = $image->getClientOriginalExtension();
        $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $extension;
        $image->move(public_path($directory), $imageName);
        return $imageName; // Return only the image name
    }

    public function deleteImage(string $imageName): bool
    {
        $fullPath = public_path('uploads/all_photo/' . $imageName); // Adjust directory as needed
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}


