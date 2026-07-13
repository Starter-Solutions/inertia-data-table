<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
