<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the value starts with a number
        if (is_numeric(substr($value, 0, 1))) {
            $fail('The :attribute must not start with a number.');
        }

        // Check if the value is a string
        if (!is_string($value)) {
            $fail('The :attribute must be a valid string.');
        }
    }
}
