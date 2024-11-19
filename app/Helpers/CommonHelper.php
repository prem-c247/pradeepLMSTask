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
}
