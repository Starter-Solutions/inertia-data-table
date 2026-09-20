<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####-??'),
            'stock' => fake()->numberBetween(0, 250),
            'price' => fake()->randomFloat(2, 5, 800),
            'is_active' => fake()->boolean(85),
        ];
    }
}
