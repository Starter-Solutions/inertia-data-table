<?php

namespace App\DataTableSorts;

use Illuminate\Database\Eloquent\Builder;
use StarterSolutions\InertiaDataTable\Contracts\SortCallback;

class NameLengthSort implements SortCallback
{
    public function __invoke(Builder $query, string $direction): void
    {
        $query->orderByRaw("length(name) {$direction}");
    }
}
