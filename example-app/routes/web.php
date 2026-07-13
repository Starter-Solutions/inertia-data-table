<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;

Route::get('/', function () {
    $allowedSorts = ['id', 'name', 'email', 'created_at'];
    $requestedSort = request('sort_by', 'id');
    $sortBy = in_array($requestedSort, $allowedSorts, true) ? $requestedSort : 'id';

    $users = User::query()
        ->select(['id', 'name', 'email', 'created_at'])
        ->dataTable(
            tableKey: 'users',
            perPage: null,
            columns: ['id', 'name', 'email', 'created_at'],
            sortBy: $sortBy,
            filterUsing: function (Builder $query, ?array $filter): void {
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
