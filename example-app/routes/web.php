<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $allowedSorts = ['id', 'name', 'email', 'created_at'];

    $users = User::query()
        ->select(['id', 'name', 'email', 'created_at'])
        ->dataTable(
            tableKey: 'users',
            columns: ['id', 'name', 'email', 'created_at'],
            filterUsing: function (Builder $query, $filter): void {
                $search = trim((string) ($filter['search'] ?? ''));

                if ($search === '') {
                    return;
                }

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            },
            additional: [
                'allowedSorts' => $allowedSorts,
            ],
        );

    return Inertia::render('Users/Index', [
        'users' => $users,
    ]);
});

Route::get('/multiple-tables', function () {
    $search = function (array $columns): Closure {
        return function (Builder $query, $filter) use ($columns): void {
            $value = trim((string) ($filter['search'] ?? ''));

            if ($value === '') {
                return;
            }

            $query->where(function (Builder $query) use ($columns, $value): void {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%{$value}%");
                }
            });
        };
    };

    return Inertia::render('MultipleTables/Index', [
        'users' => User::query()->dataTable(
            tableKey: 'users',
            columns: ['id', 'name', 'email', 'email_verified_at'],
            filterUsing: $search(['name', 'email']),
            defaultPerPage: 5,
        ),
        'products' => Product::query()->dataTable(
            tableKey: 'products',
            columns: ['id', 'name', 'sku', 'stock', 'price', 'is_active'],
            filterUsing: $search(['name', 'sku']),
            defaultPerPage: 5,
            defaultSortBy: 'sku',
            allowedSorts: ['id', 'name', 'price'],
        ),
        'orders' => Order::query()->dataTable(
            tableKey: 'orders',
            columns: ['id', 'order_number', 'customer_name', 'status', 'total', 'ordered_at'],
            filterUsing: $search(['order_number', 'customer_name', 'status']),
            defaultPerPage: 5,
            defaultSortBy: 'ordered_at',
        ),
        'tickets' => SupportTicket::query()->dataTable(
            tableKey: 'tickets',
            columns: ['id', 'subject', 'requester_email', 'priority', 'is_resolved', 'last_reply_at'],
            filterUsing: $search(['subject', 'requester_email', 'priority']),
            defaultPerPage: 5,
        ),
    ]);
})->name('multiple-tables');
