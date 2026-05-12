<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Products</h2>
                <p class="text-muted-foreground">Manage your product inventory</p>
            </div>
            <div class="flex items-center gap-3">
                <router-link to="/inventory/products/create" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">
                    Add Product
                </router-link>
            </div>
        </div>

        <div class="mb-4 flex flex-wrap items-center gap-3">
            <input
                v-model.trim="searchInput"
                type="text"
                placeholder="Search products..."
                class="rounded-md border px-3 py-2 text-sm"
                @keyup.enter="applyFilters"
            />
            <select v-model="filters.category_id" class="rounded-md border px-3 py-2 text-sm" @change="applyFilters">
                <option value="">All Categories</option>
                <option v-for="cat in categoryStore.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <label class="flex items-center gap-2 text-sm">
                <input v-model="filters.low_stock" type="checkbox" class="rounded" @change="applyFilters" />
                Low Stock Only
            </label>
            <button @click="resetFilters" class="rounded-md border px-3 py-2 text-sm hover:bg-muted">Clear</button>
            <button @click="exportCsv" class="rounded-md border px-3 py-2 text-sm hover:bg-muted ml-auto">Export CSV</button>
        </div>

        <div v-if="store.loading" class="text-center py-12 text-muted-foreground">Loading...</div>

        <template v-else>
            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-2 text-left">
                                <button @click="toggleSort('sku')" class="hover:text-foreground">SKU {{ sortIcon('sku') }}</button>
                            </th>
                            <th class="px-4 py-2 text-left">
                                <button @click="toggleSort('name')" class="hover:text-foreground">Name {{ sortIcon('name') }}</button>
                            </th>
                            <th class="px-4 py-2 text-left">Category</th>
                            <th class="px-4 py-2 text-right">
                                <button @click="toggleSort('price')" class="hover:text-foreground">Price {{ sortIcon('price') }}</button>
                            </th>
                            <th class="px-4 py-2 text-right">
                                <button @click="toggleSort('stock_quantity')" class="hover:text-foreground">Stock {{ sortIcon('stock_quantity') }}</button>
                            </th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in store.products" :key="product.id" class="border-b hover:bg-muted/50">
                            <td class="px-4 py-2 font-mono text-xs">{{ product.sku }}</td>
                            <td class="px-4 py-2 font-medium">{{ product.name }}</td>
                            <td class="px-4 py-2 text-muted-foreground">{{ product.category?.name ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">${{ formatNumber(product.price) }}</td>
                            <td class="px-4 py-2 text-right">{{ product.stock_quantity }} {{ product.unit }}</td>
                            <td class="px-4 py-2 text-center">
                                <span v-if="product.is_low_stock" class="rounded-full bg-destructive/10 px-2 py-0.5 text-xs text-destructive">Low</span>
                                <span v-else class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700 dark:bg-green-900/30 dark:text-green-400">OK</span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <router-link :to="`/inventory/products/${product.id}`" class="rounded p-1 hover:bg-muted text-muted-foreground hover:text-foreground">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </router-link>
                                    <router-link :to="`/inventory/products/${product.id}/edit`" class="rounded p-1 hover:bg-muted text-muted-foreground hover:text-foreground">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </router-link>
                                    <button @click="confirmDelete(product)" class="rounded p-1 hover:bg-muted text-muted-foreground hover:text-destructive">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="store.products.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-muted-foreground">No products found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="store.lastPage > 1" class="mt-4 flex items-center justify-between">
                <p class="text-sm text-muted-foreground">Showing {{ store.products.length > 0 ? (store.currentPage - 1) * store.filters.per_page + 1 : 0 }}–{{ Math.min(store.currentPage * store.filters.per_page, store.total) }} of {{ store.total }}</p>
                <div class="flex gap-1">
                    <button
                        v-for="page in visiblePages"
                        :key="page"
                        @click="goToPage(page)"
                        class="rounded-md border px-3 py-1 text-sm"
                        :class="page === store.currentPage ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'"
                    >{{ page }}</button>
                </div>
            </div>
        </template>

        <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showDeleteConfirm = false">
            <div class="w-full max-w-md rounded-lg border bg-card p-6 shadow-lg">
                <h3 class="mb-2 text-lg font-semibold">Delete Product</h3>
                <p class="mb-4 text-sm text-muted-foreground">Are you sure you want to delete "{{ deleteTarget?.name }}"? This action cannot be undone.</p>
                <div class="flex justify-end gap-2">
                    <button @click="showDeleteConfirm = false" class="rounded-md border px-4 py-2 text-sm hover:bg-muted">Cancel</button>
                    <button @click="doDelete" class="rounded-md bg-destructive px-4 py-2 text-sm text-destructive-foreground hover:bg-destructive/90">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useProductStore } from '../stores/products';
import { useCategoryStore } from '../stores/categories';
import type { Product } from '@/types/inventory';
import axios from 'axios';

const store = useProductStore();
const categoryStore = useCategoryStore();

const searchInput = ref('');
const filters = reactive({
    category_id: '',
    low_stock: false,
});
const showDeleteConfirm = ref(false);
const deleteTarget = ref<Product | null>(null);

const visiblePages = computed(() => {
    const pages: number[] = [];
    const start = Math.max(1, store.currentPage - 2);
    const end = Math.min(store.lastPage, store.currentPage + 2);
    for (let i = start; i <= end; i++) pages.push(i);
    return pages;
});

onMounted(async () => {
    await Promise.all([store.fetchProducts(), categoryStore.fetchCategories()]);
});

function applyFilters() {
    store.setFilters({ search: searchInput.value, category_id: filters.category_id, low_stock: filters.low_stock });
    store.fetchProducts(1);
}

function resetFilters() {
    searchInput.value = '';
    filters.category_id = '';
    filters.low_stock = false;
    store.resetFilters();
    store.fetchProducts(1);
}

function toggleSort(field: string) {
    if (store.filters.sort === field) {
        store.filters.direction = store.filters.direction === 'asc' ? 'desc' : 'asc';
    } else {
        store.filters.sort = field;
        store.filters.direction = 'asc';
    }
    store.fetchProducts(store.currentPage);
}

function sortIcon(field: string) {
    if (store.filters.sort !== field) return '';
    return store.filters.direction === 'asc' ? '↑' : '↓';
}

function goToPage(page: number) {
    store.fetchProducts(page);
}

function confirmDelete(product: Product) {
    deleteTarget.value = product;
    showDeleteConfirm.value = true;
}

async function doDelete() {
    if (!deleteTarget.value) return;
    await store.deleteProduct(deleteTarget.value.id);
    showDeleteConfirm.value = false;
    store.fetchProducts(store.currentPage);
}

function exportCsv() {
    const params = new URLSearchParams();
    if (searchInput.value) params.set('search', searchInput.value);
    if (filters.low_stock) params.set('low_stock', '1');
    window.open(`/api/export/csv?${params.toString()}`, '_blank');
}

function formatNumber(n: number | string): string {
    return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
