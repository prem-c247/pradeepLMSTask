<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ImageUploadRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the file is an image
        if (!$value->isValid() || !$value->isFile() || !in_array($value->getMimeType(), ['image/png', 'image/jpeg'])) {
            $fail('The :attribute must be a valid image file (PNG or JPG).');
        }

        // Check file size (2 MB = 2048 KB)
        if ($value->getSize() > 2048 * 1024) {
            $fail('The :attribute must not exceed 2 MB in size.');
        }
    }
}
