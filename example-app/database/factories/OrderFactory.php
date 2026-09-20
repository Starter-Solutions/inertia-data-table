<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => fake()->unique()->bothify('ORD-#####'),
            'customer_name' => fake()->name(),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'cancelled']),
            'total' => fake()->randomFloat(2, 15, 2500),
            'ordered_at' => fake()->dateTimeBetween('-6 months'),
        ];
    }
}
