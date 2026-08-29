<?php

namespace Database\Factories;

use App\Enums\AssetCategory;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => strtoupper(fake()->company()).' LTDA',
            'contact_name' => fake()->name(),
            'contact_email' => fake()->unique()->safeEmail(),
            'asset_category' => fake()->randomElement(AssetCategory::values()),
            'quantity' => fake()->numberBetween(1, 50),
            'need_summary' => fake()->sentence(12),
        ];
    }
}
