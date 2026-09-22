<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_multiple_tables_page_contains_four_independent_data_tables(): void
    {
        User::factory(7)->create();
        Product::factory(8)->create();
        Order::factory(9)->create();
        SupportTicket::factory(10)->create();

        $this->get('/multiple-tables')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('MultipleTables/Index')
                ->has('users.data', 5)
                ->where('users.total', 7)
                ->where('users.allowed_sorts', ['id', 'name', 'email', 'email_verified_at'])
                ->has('products.data', 5)
                ->where('products.total', 8)
                ->where('products.allowed_sorts', ['id', 'name', 'price'])
                ->where('products.sort_by', null)
                ->has('orders.data', 5)
                ->where('orders.total', 9)
                ->where('orders.sort_by', 'ordered_at')
                ->has('tickets.data', 5)
                ->where('tickets.total', 10));
    }

    public function test_a_table_query_only_changes_the_targeted_table(): void
    {
        User::factory(2)->create();
        Product::factory()->create(['name' => 'Matching product']);
        Product::factory()->create(['name' => 'Other product']);

        $this->get('/multiple-tables?tableKey=products&filter[search]=Matching')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('users.total', 2)
                ->where('products.total', 1)
                ->where('products.data.0.name', 'Matching product'));
    }

    public function test_an_invalid_sort_does_not_apply_ordering_without_a_default_sort(): void
    {
        User::factory(2)->create();

        $this->get('/multiple-tables?tableKey=users&sort_by=frontend_only')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('users.sort_by', null)
                ->has('users.data', 2));
    }
}
