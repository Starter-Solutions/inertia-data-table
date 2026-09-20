<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupportTicket> */
class SupportTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(5),
            'requester_email' => fake()->safeEmail(),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'is_resolved' => fake()->boolean(45),
            'last_reply_at' => fake()->optional()->dateTimeBetween('-30 days'),
        ];
    }
}
