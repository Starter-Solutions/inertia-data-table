<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Profile;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
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

    public function test_the_single_table_contains_eager_loaded_profile_columns(): void
    {
        $user = User::factory()->create();
        Profile::factory()->for($user)->create([
            'display_name' => 'Visible profile',
            'city' => 'Berlin',
            'company' => 'Starter Solutions',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('users.data.0.profile.display_name', 'Visible profile')
                ->where('users.data.0.profile.city', 'Berlin')
                ->where('users.data.0.profile.company', 'Starter Solutions'));
    }

    public function test_the_single_table_can_sort_by_a_related_profile_column(): void
    {
        $second = User::factory()->create(['name' => 'Second']);
        Profile::factory()->for($second)->create(['display_name' => 'Zulu']);

        $first = User::factory()->create(['name' => 'First']);
        Profile::factory()->for($first)->create(['display_name' => 'Alpha']);

        $this->get('/?tableKey=users&sort_by=profile.display_name&descending=0')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('users.sort_by', 'profile.display_name')
                ->where('users.allowed_sorts', [
                    'id',
                    'name',
                    'display_name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'profile.display_name',
                    'profile.city',
                    'profile.company',
                ])
                ->where('users.data.0.name', 'First')
                ->where('users.data.1.name', 'Second'));
    }

    public function test_an_accessor_sort_can_map_to_a_database_column(): void
    {
        User::factory()->create(['name' => 'Zulu']);
        User::factory()->create(['name' => 'Alpha']);

        $this->get('/?tableKey=users&sort_by=display_name&descending=0')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('users.sort_by', 'display_name')
                ->where('users.data.0.display_name', 'ALPHA')
                ->where('users.data.1.display_name', 'ZULU'));
    }

    public function test_a_keyed_callback_can_define_a_custom_sort(): void
    {
        User::factory()->create(['name' => 'Longest name']);
        User::factory()->create(['name' => 'Tiny']);

        Route::get('/callback-sort-test', fn () => User::query()->dataTable(
            tableKey: 'callback-users',
            allowedSorts: [
                'name_length' => fn (Builder $query, string $direction) => $query->orderByRaw("length(name) {$direction}"),
            ],
        ));

        $this->get('/callback-sort-test?tableKey=callback-users&sort_by=name_length&descending=0')
            ->assertOk()
            ->assertJsonPath('sort_by', 'name_length')
            ->assertJsonPath('allowed_sorts', ['name_length'])
            ->assertJsonPath('data.0.name', 'Tiny')
            ->assertJsonPath('data.1.name', 'Longest name');
    }

    public function test_an_allowed_sort_for_a_missing_model_column_is_logged_and_ignored(): void
    {
        User::factory()->create();
        Log::spy();

        Route::get('/missing-sort-test', fn () => User::query()->dataTable(
            tableKey: 'missing-sort-users',
            allowedSorts: ['missing_attribute'],
        ));

        $this->get('/missing-sort-test?tableKey=missing-sort-users&sort_by=missing_attribute')
            ->assertOk()
            ->assertJsonPath('sort_by', null)
            ->assertJsonPath('allowed_sorts', ['missing_attribute'])
            ->assertJsonCount(1, 'data');

        Log::shouldHaveReceived('warning')->once()->withArgs(
            fn (string $message, array $context): bool => $message === 'Inertia Data Table ignored an invalid allowed sort.'
                && $context['table_key'] === 'missing-sort-users'
                && $context['model'] === User::class
                && $context['table'] === 'users'
                && $context['sort'] === 'missing_attribute'
                && $context['column'] === 'missing_attribute'
                && $context['reason'] === 'column_not_found',
        );
    }

    public function test_an_allowed_sort_for_a_missing_relation_column_is_logged_and_ignored(): void
    {
        User::factory()->create();
        Log::spy();

        Route::get('/missing-relation-sort-test', fn () => User::query()
            ->with('profile')
            ->dataTable(
                tableKey: 'missing-relation-sort-users',
                allowedSorts: ['profile.missing_attribute'],
            ));

        $this->get('/missing-relation-sort-test?tableKey=missing-relation-sort-users&sort_by=profile.missing_attribute')
            ->assertOk()
            ->assertJsonPath('sort_by', null)
            ->assertJsonCount(1, 'data');

        Log::shouldHaveReceived('warning')->once()->withArgs(
            fn (string $message, array $context): bool => $message === 'Inertia Data Table ignored an invalid allowed sort.'
                && $context['table_key'] === 'missing-relation-sort-users'
                && $context['model'] === Profile::class
                && $context['table'] === 'profiles'
                && $context['sort'] === 'profile.missing_attribute'
                && $context['relation'] === 'profile'
                && $context['column'] === 'missing_attribute'
                && $context['reason'] === 'related_column_not_found',
        );
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
                ->where('users.allowed_sorts', ['id', 'name', 'display_name', 'email', 'email_verified_at', 'created_at'])
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
