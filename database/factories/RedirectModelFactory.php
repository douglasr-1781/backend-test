<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RedirectModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'url_to' => fake()->unique()->url(),
            'active' => fake()->boolean(),
            'created_at' => fake()->date(),
            'deleted_at' => fake()->date(),
        ];
    }
}
