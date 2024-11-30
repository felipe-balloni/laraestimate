<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EstimateFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => Str::title($this->faker->sentence(3)),
            'expiration_date' => $this->faker->date(),
            'allows_to_select_items' => $this->faker->boolean(),
        ];
    }
}
