<script setup>
import { useDataTable } from '@starter-solutions/inertia-data-table-vue';
import { ArrowDown, ArrowUp, ChevronsUpDown, Search } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const props = defineProps({
    tableKey: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, required: true },
    columns: { type: Array, required: true },
});

const {
    data,
    pagination,
    allowedSorts,
    filter,
    previousPage,
    nextPage,
    itemsPerPage,
    sortBy,
    setFilter,
    resetFilters,
} = useDataTable(props.tableKey, {
    useUrlQuery: true,
    replaceHistory: true,
});

const search = ref(filter.search ?? '');
const pageSize = computed({
    get: () => String(pagination.value.per_page),
    set: (value) => itemsPerPage(Number(value)),
});

const applySearch = () => {
    const value = search.value.trim();

    if (value === '') {
        resetFilters();
    } else {
        setFilter('search', value);
    }
};

const resetSearch = () => {
    search.value = '';
    resetFilters();
};

const sortIcon = (key) => {
    if (pagination.value.sort_by !== key) {
        return ChevronsUpDown;
    }

    return pagination.value.descending ? ArrowDown : ArrowUp;
};

const displayValue = (row, column) => {
    const value = row[column.key];

    if (column.format === 'boolean') {
        return value ? 'Yes' : 'No';
    }

    if (column.format === 'currency') {
        return new Intl.NumberFormat('en', { style: 'currency', currency: 'EUR' }).format(value);
    }

    if (column.format === 'date') {
        return value ? new Intl.DateTimeFormat('en', { dateStyle: 'medium' }).format(new Date(value)) : '—';
    }

    return value ?? '—';
};
</script>

<template>
    <Card class="min-w-0">
        <CardHeader class="gap-4 border-b lg:flex-row lg:items-start lg:justify-between">
            <div>
                <CardTitle>{{ title }}</CardTitle>
                <CardDescription>{{ description }}</CardDescription>
            </div>
            <form class="flex gap-2" @submit.prevent="applySearch">
                <Input v-model="search" class="w-48" :placeholder="`Search ${title.toLowerCase()}`" type="search" />
                <Button size="sm" type="submit" variant="secondary"><Search /> Search</Button>
                <Button size="sm" type="button" variant="ghost" @click="resetSearch">Reset</Button>
            </form>
        </CardHeader>

        <CardContent class="overflow-x-auto p-0">
            <Table>
                <TableHeader>
                    <TableRow class="bg-muted/40 hover:bg-muted/40">
                        <TableHead v-for="column in columns" :key="column.key">
                            <Button v-if="allowedSorts.includes(column.key)" class="-ml-3 h-8 px-3" size="sm" type="button" variant="ghost" @click="sortBy(column.key)">
                                {{ column.label }}
                                <component :is="sortIcon(column.key)" />
                            </Button>
                            <span v-else class="font-medium">{{ column.label }}</span>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="row in data" :key="row.id">
                        <TableCell v-for="column in columns" :key="column.key" :class="column.key === 'id' ? 'font-medium' : ''">
                            {{ displayValue(row, column) }}
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="data.length === 0">
                        <TableCell class="text-muted-foreground h-20 text-center" :colspan="columns.length">No results.</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>

        <div class="flex items-center justify-between gap-3 border-t px-4 py-3">
            <div class="text-muted-foreground text-sm">{{ pagination.from ?? 0 }}–{{ pagination.to ?? 0 }} of {{ pagination.total }}</div>
            <div class="flex items-center gap-2">
                <select v-model="pageSize" class="border-input bg-background h-8 rounded-md border px-2 text-sm">
                    <option value="5">5 rows</option>
                    <option value="10">10 rows</option>
                    <option value="0">All rows</option>
                </select>
                <Button size="sm" type="button" variant="outline" :disabled="pagination.current_page === 1" @click="previousPage">Previous</Button>
                <span class="text-muted-foreground text-sm">{{ pagination.current_page }}/{{ pagination.last_page }}</span>
                <Button size="sm" type="button" variant="outline" :disabled="pagination.current_page === pagination.last_page" @click="nextPage">Next</Button>
            </div>
        </div>
    </Card>
</template>
