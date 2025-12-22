<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->bothify('SKU-#####'),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'cost_price' => $this->faker->randomFloat(2, 5, 500),
            'sale_price' => $this->faker->randomFloat(2, 10, 800),
        ];
    }
}
