<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Str;

class CommonHelper
{
    public static function fileUpload($file, $dir)
    {
        try {
            if (!empty($file)) {
                $fileName = time() . Str::random(4) . "." . $file->getClientOriginalExtension();
                $uploadPath = public_path(UPLOAD_PATH . $dir);

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $fileName);
                return $fileName;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteImageByUrl($fileUrl)
    {
        // do not remove default images
        $defaultUserImage = NO_PROFILE;
        $defaultImg = DEFAULT_IMAGE;

        if ($fileUrl === asset($defaultUserImage)) {
            return false;
        }

        if ($fileUrl === asset($defaultImg)) {
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
        $localPath = public_path(UPLOAD_PATH . $directory . '/' . $fileName);

        if (file_exists($localPath)) {
            unlink($localPath);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate a non-repeating random integer of a specified length.
     *
     * @param int $length Length of the desired number.
     * @return int The generated number.
     * @throws Exception If the length is greater than 10 or invalid.
     */
    public static function generateUniqueNumber(int $length): int
    {
        if ($length < 1 || $length > 10) {
            throw new Exception('Length must be between 1 and 10.');
        }

        $digits = range(0, 9);
        shuffle($digits);

        // Take the first $length digits and ensure the first digit isn't zero.
        if ($length > 1 && $digits[0] === 0) {
            $nonZeroIndex = array_search(max(array_slice($digits, 1)), $digits);
            [$digits[0], $digits[$nonZeroIndex]] = [$digits[$nonZeroIndex], $digits[0]];
        }

        return (int)implode('', array_slice($digits, 0, $length));
    }
    
    public static function getAddressValidationRules()
    {
        return [
            'address_1' => 'required|string|max:100',
            'address_2' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:50',
            'city' => 'required|string|max:50',
            'postal_code' => 'required|integer',
            'state' => 'required|string|max:50',
            'country' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean|in:0,1',
        ];
    }
}
