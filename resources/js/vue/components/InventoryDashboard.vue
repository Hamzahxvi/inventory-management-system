<template>
    <div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Inventory Dashboard</h2>
            <p class="text-muted-foreground">Overview of your inventory status</p>
        </div>

        <div v-if="loading" class="text-center py-12 text-muted-foreground">Loading...</div>

        <template v-else-if="report">
            <div class="mb-6 grid grid-cols-4 gap-4">
                <div class="rounded-lg border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Total Products</p>
                    <p class="text-2xl font-bold">{{ report.totals?.total_products ?? 0 }}</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Total Stock</p>
                    <p class="text-2xl font-bold">{{ report.totals?.total_stock ?? 0 }}</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Inventory Value</p>
                    <p class="text-2xl font-bold">${{ formatNumber(report.totals?.total_value ?? 0) }}</p>
                </div>
                <div class="rounded-lg border bg-card p-4 border-destructive/50">
                    <p class="text-sm text-destructive">Low Stock Items</p>
                    <p class="text-2xl font-bold text-destructive">{{ report.totals?.low_stock_count ?? 0 }}</p>
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="border-b px-4 py-3">
                    <h3 class="font-semibold">Inventory by Category</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-2 text-left">Category</th>
                                <th class="px-4 py-2 text-right">Products</th>
                                <th class="px-4 py-2 text-right">Stock</th>
                                <th class="px-4 py-2 text-right">Value</th>
                                <th class="px-4 py-2 text-right">Low Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="cat in report.by_category" :key="cat.category" class="border-b">
                                <td class="px-4 py-2 font-medium">{{ cat.category }}</td>
                                <td class="px-4 py-2 text-right">{{ cat.total_products }}</td>
                                <td class="px-4 py-2 text-right">{{ cat.total_stock }}</td>
                                <td class="px-4 py-2 text-right">${{ formatNumber(cat.inventory_value) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <span v-if="cat.low_stock_count > 0" class="rounded-full bg-destructive/10 px-2 py-0.5 text-xs text-destructive">{{ cat.low_stock_count }}</span>
                                    <span v-else class="text-muted-foreground">0</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import type { ReportData } from '@/types/inventory';

const report = ref<ReportData | null>(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await axios.get<ReportData>('/api/reports/inventory-summary');
        report.value = data;
    } finally {
        loading.value = false;
    }
});

function formatNumber(n: number | string): string {
    return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
