<?php

namespace Database\Factories;

use App\Models\Recommend;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecommendPlay>
 */
class RecommendPlayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recommend_id' => Recommend::factory(),
        ];
    }
}
