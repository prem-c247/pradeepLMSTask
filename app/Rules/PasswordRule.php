<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        // if (!preg_match($pattern, $value)) {
        //     $fail("The {$attribute} must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");
        // }


        if (is_string($value) && strlen($value) > 6 && strlen($value) <= 255) {
            $fail("The {$attribute} must be at least 6 characters long.");
        }
    }
}
