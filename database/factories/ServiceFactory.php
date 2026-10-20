<?php

namespace Database\Factories;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(3),
            'name' => fake()->words(3, true),
            'category' => fake()->randomElement(ServiceCategory::cases()),
            'price' => fake()->numberBetween(15, 500) * 10,
            'duration_minutes' => fake()->randomElement([30, 45, 60]),
            'description' => fake()->sentence(14),
            'benefits' => fake()->sentences(3),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function quoteOnly(): static
    {
        return $this->state(fn () => ['price' => null]);
    }
}
