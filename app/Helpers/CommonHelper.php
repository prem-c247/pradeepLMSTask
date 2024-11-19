<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommonHelper
{
    public static function fileUpload($file, $dir)
    {
        try {
            if (!empty($file)) {
                $fileName = time() . Str::random(4) . "." . $file->getClientOriginalExtension();

                $uploadPath = public_path('uploads/' . $dir);

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $fileName);

                // return 'uploads/' . $dir . '/' . $fileName;
                return $fileName;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());

            return false;
        }
    }

    public static function deleteImageByUrl($fileUrl)
    {
        // do not remove default images
        $noUserImage    =   NO_PROFILE;
        $defaultImg     =   DEFAULT_IMAGE;

        if ($fileUrl == asset($noUserImage)) {
            return false;
        }

        if ($fileUrl == asset($defaultImg)) {
            return false;
        }

        $appUrl = config('app.url');

        $relativePath = str_replace($appUrl, '', $fileUrl);

        $localPath = public_path($relativePath);

        if (file_exists($localPath)) {
            unlink($localPath);
            return true;
        } else {
            return false;
        }
    }

    public static function deleteImageByName($fileName, $directory)
    {
        $localPath = public_path('uploads/' . $directory . '/' . $fileName);

        if (file_exists($localPath)) {
            unlink($localPath);
            return true;
        } else {
            return false;
        }
    }
}
