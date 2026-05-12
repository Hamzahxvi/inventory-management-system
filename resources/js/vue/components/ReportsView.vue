<template>
    <div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Reports</h2>
            <p class="text-muted-foreground">Inventory analytics and data export</p>
        </div>

        <div v-if="loading" class="text-center py-12 text-muted-foreground">Loading...</div>

        <template v-else>
            <div class="mb-6 grid grid-cols-4 gap-4">
                <div class="rounded-lg border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Total Products</p>
                    <p class="text-2xl font-bold">{{ summary?.totals?.total_products ?? 0 }}</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Inventory Value</p>
                    <p class="text-2xl font-bold">${{ formatNum(summary?.totals?.total_value ?? 0) }}</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Total Stock Units</p>
                    <p class="text-2xl font-bold">{{ summary?.totals?.total_stock ?? 0 }}</p>
                </div>
                <div class="rounded-lg border bg-card p-4 border-destructive/50">
                    <p class="text-sm text-destructive">Low Stock Items</p>
                    <p class="text-2xl font-bold text-destructive">{{ summary?.totals?.low_stock_count ?? 0 }}</p>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-6">
                <div class="rounded-lg border">
                    <div class="border-b px-4 py-3">
                        <h3 class="font-semibold">Top Products by Value</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-right">Price</th>
                                <th class="px-4 py-2 text-right">Stock</th>
                                <th class="px-4 py-2 text-right">Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in topProducts?.top_by_value" :key="p.id" class="border-b">
                                <td class="px-4 py-2 font-medium">{{ p.name }}</td>
                                <td class="px-4 py-2 text-right">${{ formatNum(p.price) }}</td>
                                <td class="px-4 py-2 text-right">{{ p.stock_quantity }}</td>
                                <td class="px-4 py-2 text-right font-semibold">${{ formatNum(p.total_value) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="rounded-lg border">
                    <div class="border-b px-4 py-3">
                        <h3 class="font-semibold">Most Active Products</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-right">Stock</th>
                                <th class="px-4 py-2 text-right">Movements</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in topProducts?.top_by_movement" :key="p.id" class="border-b">
                                <td class="px-4 py-2 font-medium">{{ p.name }}</td>
                                <td class="px-4 py-2 text-right">{{ p.stock_quantity }}</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ p.total_movements }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <h3 class="font-semibold">Inventory by Category</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-2 text-left">Category</th>
                                <th class="px-4 py-2 text-right">Products</th>
                                <th class="px-4 py-2 text-right">Total Stock</th>
                                <th class="px-4 py-2 text-right">Value</th>
                                <th class="px-4 py-2 text-right">Cost</th>
                                <th class="px-4 py-2 text-right">Low Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="cat in summary?.by_category" :key="cat.category" class="border-b">
                                <td class="px-4 py-2 font-medium">{{ cat.category }}</td>
                                <td class="px-4 py-2 text-right">{{ cat.total_products }}</td>
                                <td class="px-4 py-2 text-right">{{ cat.total_stock }}</td>
                                <td class="px-4 py-2 text-right">${{ formatNum(cat.inventory_value) }}</td>
                                <td class="px-4 py-2 text-right">${{ formatNum(cat.inventory_cost) }}</td>
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
import type { ReportData, TopProductsReport } from '@/types/inventory';

const summary = ref<ReportData | null>(null);
const topProducts = ref<TopProductsReport | null>(null);
const loading = ref(true);

onMounted(async () => {
    const [summaryRes, topRes] = await Promise.all([
        axios.get<ReportData>('/api/reports/inventory-summary'),
        axios.get<TopProductsReport>('/api/reports/top-products', { params: { limit: 5 } }),
    ]);
    summary.value = summaryRes.data;
    topProducts.value = topRes.data;
    loading.value = false;
});

function formatNum(n: number | string): string {
    return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
