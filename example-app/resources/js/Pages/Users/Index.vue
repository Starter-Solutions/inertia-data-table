<script setup>
import { Head } from '@inertiajs/vue3';
import { useDataTable } from '@starter-solutions/inertia-data-table-vue';
import {
    ArrowDown,
    ArrowUp,
    ChevronsUpDown,
    RotateCcw,
    Search,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const {
    data: users,
    pagination,
    filter,
    additional,
    firstPage,
    previousPage,
    nextPage,
    lastPage,
    goToPage,
    itemsPerPage,
    sortBy,
    setFilter,
    resetFilters,
} = useDataTable('users', { useUrlQuery: true });

const search = ref(filter.search ?? '');

const columns = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'created_at', label: 'Created' },
];

const pageSize = computed({
    get: () => String(pagination.value.per_page),
    set: (value) => itemsPerPage(Number(value)),
});

const rangeLabel = computed(() => {
    if (!pagination.value.total) {
        return 'No results';
    }

    return `${pagination.value.from}-${pagination.value.to} of ${pagination.value.total}`;
});

const applySearch = () => {
    const value = search.value.trim();

    if (value === '') {
        resetFilters();
        return;
    }

    setFilter('search', value);
};

const formatDate = (value) =>
    new Intl.DateTimeFormat('en', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    }).format(new Date(value));

const SortIcon = (key) => {
    if (pagination.value.sort_by !== key) {
        return ChevronsUpDown;
    }

    return pagination.value.descending ? ArrowDown : ArrowUp;
};
</script>

<template>
    <Head title="Users" />

    <main class="mx-auto flex min-h-screen w-full max-w-6xl flex-col gap-6 px-6 py-8">
        <header class="flex flex-col gap-3 border-b pb-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-muted-foreground text-sm font-medium uppercase tracking-wide">Local package test</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-normal">Inertia Data Table</h1>
            </div>

            <form class="flex w-full gap-2 md:w-auto" @submit.prevent="applySearch">
                <div class="relative min-w-0 flex-1 md:w-72">
                    <Search class="text-muted-foreground pointer-events-none absolute left-2.5 top-2.5 size-4" />
                    <Input
                        v-model="search"
                        class="pl-8"
                        placeholder="Search users"
                        type="search"
                    />
                </div>
                <Button type="submit">
                    <Search />
                    Search
                </Button>
                <Button variant="outline" type="button" @click="search = ''; resetFilters()">
                    <RotateCcw />
                    Reset
                </Button>
            </form>
        </header>

        <Card>
            <CardHeader class="border-b">
                <div>
                    <CardTitle>Users</CardTitle>
                    <CardDescription>{{ rangeLabel }}</CardDescription>
                </div>
            </CardHeader>

            <CardContent class="p-0">
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/40 hover:bg-muted/40">
                            <TableHead v-for="column in columns" :key="column.key">
                                <Button
                                    class="-ml-3 h-8 px-3"
                                    size="sm"
                                    type="button"
                                    variant="ghost"
                                    @click="sortBy(column.key)"
                                >
                                    {{ column.label }}
                                    <component :is="SortIcon(column.key)" />
                                </Button>
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users" :key="user.id">
                            <TableCell class="font-medium">{{ user.id }}</TableCell>
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ user.email }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ formatDate(user.created_at) }}</TableCell>
                        </TableRow>
                        <TableRow v-if="users.length === 0">
                            <TableCell class="text-muted-foreground h-24 text-center" colspan="4">
                                No users found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>

            <div class="flex flex-col gap-3 border-t px-4 py-3 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-wrap items-center gap-3">
                    <p class="text-muted-foreground text-sm">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </p>

                    <label class="text-muted-foreground flex items-center gap-2 text-sm">
                        <select
                            v-model="pageSize"
                            class="border-input bg-background ring-offset-background h-9 w-24 rounded-md border px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                        >
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="0">All</option>
                        </select>
                        Rows
                    </label>
                </div>

                <Pagination
                    class="mx-0 w-auto justify-start md:justify-end"
                    :items-per-page="pagination.per_page"
                    :page="pagination.current_page"
                    :sibling-count="1"
                    :total="pagination.per_page === 0 ? 0 :pagination.total"
                    show-edges
                    @update:page="goToPage"
                >
                    <PaginationContent v-slot="{ items }">
                        <PaginationFirst :disabled="pagination.current_page === 1" @click="firstPage" />
                        <PaginationPrevious :disabled="pagination.current_page === 1" @click="previousPage" />
                        <template v-for="(item, index) in items" :key="`${item.type}-${item.value ?? index}`">
                            <PaginationItem
                                v-if="item.type === 'page'"
                                :is-active="item.value === pagination.current_page"
                                :value="item.value"
                            >
                                {{ item.value }}
                            </PaginationItem>
                            <PaginationEllipsis v-else />
                        </template>
                        <PaginationNext :disabled="pagination.current_page === pagination.last_page" @click="nextPage" />
                        <PaginationLast :disabled="pagination.current_page === pagination.last_page" @click="lastPage" />
                    </PaginationContent>
                </Pagination>
            </div>
        </Card>
        <pre class="bg-blue-400/40">{{ filter }}</pre>
        <pre class="bg-red-400/40">{{ additional }}</pre>
    </main>
</template>
