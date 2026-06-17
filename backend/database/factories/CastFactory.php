<?php

namespace Database\Factories;

use App\Models\Cast;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cast>
 */
class CastFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
