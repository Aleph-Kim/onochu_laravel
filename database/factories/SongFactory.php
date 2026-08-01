<?php

namespace Database\Factories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    public function definition(): array
    {
        return [
            'album_id'  => Album::factory(),
            'title'     => fake()->sentence(3),
            'genre'     => 'K-POP',
            'title_yn'  => 'Y',
            'play_time' => '03:30',
            'lyrics'    => fake()->paragraph(),
            'composer'  => fake()->name(),
            'lyricist'  => fake()->name(),
            'arranger'  => fake()->name(),
            'flo_id'    => fake()->unique()->numberBetween(1, 999999),
        ];
    }
}
