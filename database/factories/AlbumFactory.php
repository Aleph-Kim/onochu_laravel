<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Album>
 */
class AlbumFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'        => fake()->words(3, true),
            'release_date' => fake()->date(),
            'genre'        => 'K-POP',
            'type'         => '정규',
            'img_url'      => fake()->imageUrl(),
            'flo_id'       => fake()->unique()->numberBetween(1, 999999),
        ];
    }
}
