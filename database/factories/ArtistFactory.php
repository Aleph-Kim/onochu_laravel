<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->name(),
            'genre'       => 'K-POP',
            'group_type'  => '솔로',
            'img_url'     => fake()->imageUrl(),
            'flo_id'      => fake()->unique()->numberBetween(1, 999999),
            'flo_img_url' => fake()->imageUrl(),
        ];
    }
}
