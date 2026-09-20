<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ExampleDataTable from '@/components/ExampleDataTable.vue';
import { Button } from '@/components/ui/button';

const tables = [
    {
        key: 'users', title: 'Users', description: 'Identity and verification attributes',
        columns: [
            { key: 'id', label: 'ID' }, { key: 'name', label: 'Name' }, { key: 'email', label: 'Email' },
            { key: 'email_verified_at', label: 'Verified', format: 'date' },
        ],
    },
    {
        key: 'products', title: 'Products', description: 'Inventory, pricing and active state',
        columns: [
            { key: 'id', label: 'ID' }, { key: 'name', label: 'Product' }, { key: 'sku', label: 'SKU' },
            { key: 'stock', label: 'Stock' }, { key: 'price', label: 'Price', format: 'currency' },
            { key: 'is_active', label: 'Active', format: 'boolean' },
        ],
    },
    {
        key: 'orders', title: 'Orders', description: 'Customer, workflow and monetary attributes',
        columns: [
            { key: 'id', label: 'ID' }, { key: 'order_number', label: 'Order' },
            { key: 'customer_name', label: 'Customer' }, { key: 'status', label: 'Status' },
            { key: 'total', label: 'Total', format: 'currency' }, { key: 'ordered_at', label: 'Ordered', format: 'date' },
        ],
    },
    {
        key: 'tickets', title: 'Support tickets', description: 'Priority, resolution and nullable date attributes',
        columns: [
            { key: 'id', label: 'ID' }, { key: 'subject', label: 'Subject' },
            { key: 'requester_email', label: 'Requester' }, { key: 'priority', label: 'Priority' },
            { key: 'is_resolved', label: 'Resolved', format: 'boolean' },
            { key: 'last_reply_at', label: 'Last reply', format: 'date' },
        ],
    },
];
</script>

<template>
    <Head title="Multiple tables" />
    <main class="mx-auto flex min-h-screen w-full max-w-[1500px] flex-col gap-6 px-6 py-8">
        <header class="flex items-end justify-between border-b pb-5">
            <div>
                <p class="text-muted-foreground text-sm font-medium uppercase tracking-wide">Concurrency test page</p>
                <h1 class="mt-1 text-3xl font-semibold">Four independent data tables</h1>
                <p class="text-muted-foreground mt-2">Each table has its own model, columns, filter, sorting and pagination state.</p>
            </div>
            <Button as-child variant="outline"><Link href="/">Single-table example</Link></Button>
        </header>

        <div class="grid gap-6 xl:grid-cols-2">
            <ExampleDataTable
                v-for="table in tables"
                :key="table.key"
                :table-key="table.key"
                :title="table.title"
                :description="table.description"
                :columns="table.columns"
            />
        </div>
    </main>
</template>
