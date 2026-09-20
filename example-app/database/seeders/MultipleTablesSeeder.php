<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\SupportTicket;
use Illuminate\Database\Seeder;

class MultipleTablesSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory(32)->create();
        Order::factory(28)->create();
        SupportTicket::factory(24)->create();
    }
}
