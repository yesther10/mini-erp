<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    private static int $sequence = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sequence = str_pad((string) self::$sequence++, 12, '0', STR_PAD_LEFT);

        return [
            'legal_name' => strtoupper(fake()->company()).' LTDA',
            'cnpj' => $this->generateValidCnpj($sequence),
            'street' => fake()->streetName(),
            'number' => (string) fake()->buildingNumber(),
            'district' => fake()->citySuffix(),
            'city' => fake()->city(),
            'state' => fake()->randomElement(['SP', 'RJ', 'MG', 'PR', 'SC', 'RS']),
            'zip_code' => preg_replace('/\D/', '', fake()->postcode('########')),
            'complement' => fake()->optional()->secondaryAddress(),
            'primary_contact_name' => fake()->name(),
            'primary_contact_email' => fake()->unique()->safeEmail(),
            'primary_contact_phone' => fake()->numerify('11#########'),
        ];
    }

    private function generateValidCnpj(string $base): string
    {
        $digits = array_map('intval', str_split($base));

        $digits[] = $this->calculateCheckDigit($digits, [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        $digits[] = $this->calculateCheckDigit($digits, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        return implode('', $digits);
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
