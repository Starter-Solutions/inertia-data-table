<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $testUser->profile()->create([
            'display_name' => 'tester',
            'city' => 'Berlin',
            'company' => 'Starter Solutions',
        ]);

        User::factory(49)->create()->each(fn (User $user) => $user->profile()->create([
            'display_name' => fake()->userName(),
            'city' => fake()->city(),
            'company' => fake()->company(),
        ]));
        $this->call(MultipleTablesSeeder::class);
    }
}
