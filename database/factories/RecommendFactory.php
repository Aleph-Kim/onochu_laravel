<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recommend>
 */
class RecommendFactory extends Factory
{
    public function definition(): array
    {
        return [
            'song_id' => Song::factory(),
            'user_id' => User::factory(),
            'score'   => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(),
        ];
    }
}
