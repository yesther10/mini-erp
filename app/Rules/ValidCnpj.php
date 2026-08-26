<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidCnpj implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        if (strlen($digits) !== 14 || count(array_unique(str_split($digits))) === 1) {
            $fail('The :attribute field must be a valid CNPJ.');

            return;
        }

        $numbers = array_map('intval', str_split($digits));
        $firstCheckDigit = $this->calculateCheckDigit(array_slice($numbers, 0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        $secondCheckDigit = $this->calculateCheckDigit(array_slice($numbers, 0, 13), [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        if ($numbers[12] !== $firstCheckDigit || $numbers[13] !== $secondCheckDigit) {
            $fail('The :attribute field must be a valid CNPJ.');
        }
    }

    /**
     * @param  list<int>  $digits
     * @param  list<int>  $weights
     */
    private function calculateCheckDigit(array $digits, array $weights): int
    {
        $sum = 0;

        foreach ($digits as $index => $digit) {
            $sum += $digit * $weights[$index];
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
