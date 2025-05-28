<?php

namespace Database\Factories\Player\Models;

use App\Player\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'position' => $this->faker->randomElement(config('positions.positions')),
        ];
    }
}
