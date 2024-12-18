<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class ItemFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'description' => Str::title($this->faker->sentence(5)),
            'duration' => null,
            'price' => rand(199, 599),
            'obligatory' => false,
        ];
    }
}
