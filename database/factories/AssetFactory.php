<?php

namespace Database\Factories;

use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    private static int $sequence = 1;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sequence = self::$sequence++;

        return [
            'internal_code' => sprintf('AST-%03d', $sequence),
            'serial_number' => fake()->optional()->bothify('SN-###??'),
            'category' => fake()->randomElement(AssetCategory::values()),
            'brand' => fake()->company(),
            'model' => strtoupper(fake()->bothify('Model-##??')),
            'status' => AssetStatus::Available->value,
        ];
    }
}
