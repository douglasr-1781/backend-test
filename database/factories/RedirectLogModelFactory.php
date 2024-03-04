<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RedirectLogModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'redirect_id' => 1,
            'ip' => fake()->randomElement(['a', null]),
            'referer' => 'referer',
            'query_params' => json_encode(['q' => 'teste']),
            'user_agent' => fake()->userAgent(),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date(),
        ];
    }
}
