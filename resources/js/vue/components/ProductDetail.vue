<template>
    <div>
        <div class="mb-6">
            <router-link
                to="/inventory/products"
                class="text-sm text-muted-foreground hover:text-foreground"
                >← Back to Products</router-link
            >
        </div>

        <div
            v-if="store.loading"
            class="py-12 text-center text-muted-foreground"
        >
            Loading...
        </div>

        <template v-else-if="store.currentProduct">
            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 space-y-6">
                    <div class="rounded-lg border p-6">
                        <h2 class="text-2xl font-bold">
                            {{ store.currentProduct.name }}
                        </h2>
                        <p class="mt-1 font-mono text-sm text-muted-foreground">
                            {{ store.currentProduct.sku }}
                        </p>
                        <p
                            v-if="store.currentProduct.description"
                            class="mt-3 text-sm"
                        >
                            {{ store.currentProduct.description }}
                        </p>

                        <div class="mt-4 grid grid-cols-3 gap-4">
                            <div>
                                <span class="text-xs text-muted-foreground"
                                    >Price</span
                                >
                                <p class="text-lg font-semibold">
                                    ${{
                                        formatNumber(store.currentProduct.price)
                                    }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-muted-foreground"
                                    >Cost</span
                                >
                                <p class="text-lg font-semibold">
                                    ${{
                                        formatNumber(store.currentProduct.cost)
                                    }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-muted-foreground"
                                    >Profit Margin</span
                                >
                                <p
                                    class="text-lg font-semibold"
                                    :class="
                                        margin >= 0
                                            ? 'text-green-600'
                                            : 'text-destructive'
                                    "
                                >
                                    {{ margin >= 0 ? '+' : '' }}{{ margin }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border">
                        <div
                            class="flex items-center justify-between border-b px-4 py-3"
                        >
                            <h3 class="font-semibold">Stock Movements</h3>
                            <button
                                @click="showMovementForm = !showMovementForm"
                                class="rounded-md border px-3 py-1.5 text-sm hover:bg-muted"
                            >
                                {{
                                    showMovementForm
                                        ? 'Cancel'
                                        : 'Record Movement'
                                }}
                            </button>
                        </div>

                        <div
                            v-if="showMovementForm"
                            class="border-b bg-muted/30 p-4"
                        >
                            <form
                                @submit.prevent="recordMovement"
                                class="flex items-end gap-3"
                            >
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium"
                                        >Type</label
                                    >
                                    <select
                                        v-model="movement.type"
                                        class="rounded-md border px-3 py-2 text-sm"
                                    >
                                        <option value="in">Stock In</option>
                                        <option value="out">Stock Out</option>
                                        <option value="adjustment">
                                            Adjustment
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium"
                                        >Quantity</label
                                    >
                                    <input
                                        v-model.number="movement.quantity"
                                        type="number"
                                        min="1"
                                        required
                                        class="w-24 rounded-md border px-3 py-2 text-sm"
                                    />
                                </div>
                                <div class="flex-1">
                                    <label
                                        class="mb-1 block text-xs font-medium"
                                        >Notes</label
                                    >
                                    <input
                                        v-model="movement.notes"
                                        type="text"
                                        class="w-full rounded-md border px-3 py-2 text-sm"
                                        placeholder="Optional note"
                                    />
                                </div>
                                <button
                                    type="submit"
                                    :disabled="moving"
                                    class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                                >
                                    {{ moving ? '...' : 'Record' }}
                                </button>
                            </form>
                            <p
                                v-if="movementError"
                                class="mt-2 text-sm text-destructive"
                            >
                                {{ movementError }}
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b bg-muted/50">
                                        <th class="px-4 py-2 text-left">
                                            Date
                                        </th>
                                        <th class="px-4 py-2 text-left">
                                            User
                                        </th>
                                        <th class="px-4 py-2 text-center">
                                            Type
                                        </th>
                                        <th class="px-4 py-2 text-right">
                                            Qty
                                        </th>
                                        <th class="px-4 py-2 text-right">
                                            Before
                                        </th>
                                        <th class="px-4 py-2 text-right">
                                            After
                                        </th>
                                        <th class="px-4 py-2 text-left">
                                            Notes
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="movement in store.currentProduct
                                            .stock_movements"
                                        :key="movement.id"
                                        class="border-b"
                                    >
                                        <td class="px-4 py-2">
                                            {{
                                                formatDate(movement.created_at)
                                            }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ movement.user_name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span
                                                class="rounded-full px-2 py-0.5 text-xs"
                                                :class="
                                                    typeBadgeClass(
                                                        movement.type,
                                                    )
                                                "
                                                >{{ movement.type }}</span
                                            >
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            {{ movement.quantity }}
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            {{ movement.quantity_before }}
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            {{ movement.quantity_after }}
                                        </td>
                                        <td
                                            class="px-4 py-2 text-muted-foreground"
                                        >
                                            {{ movement.notes ?? '—' }}
                                        </td>
                                    </tr>
                                    <tr
                                        v-if="
                                            !store.currentProduct
                                                .stock_movements?.length
                                        "
                                    >
                                        <td
                                            colspan="7"
                                            class="px-4 py-8 text-center text-muted-foreground"
                                        >
                                            No stock movements recorded.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-lg border p-4">
                        <h3 class="mb-3 font-semibold">Stock Status</h3>
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm text-muted-foreground"
                                >Current Stock</span
                            >
                            <span class="text-lg font-bold"
                                >{{ store.currentProduct.stock_quantity }}
                                {{ store.currentProduct.unit }}</span
                            >
                        </div>
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm text-muted-foreground"
                                >Threshold</span
                            >
                            <span
                                >{{ store.currentProduct.low_stock_threshold }}
                                {{ store.currentProduct.unit }}</span
                            >
                        </div>
                        <div class="mt-3">
                            <span
                                v-if="store.currentProduct.is_low_stock"
                                class="inline-flex items-center gap-1 rounded-full bg-destructive/10 px-2.5 py-1 text-sm text-destructive"
                            >
                                Low Stock
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400"
                            >
                                In Stock
                            </span>
                        </div>
                    </div>

                    <div class="rounded-lg border p-4">
                        <h3 class="mb-3 font-semibold">Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground"
                                    >Category</span
                                >
                                <span>{{
                                    store.currentProduct.category?.name ?? '—'
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground"
                                    >Supplier</span
                                >
                                <span>{{
                                    store.currentProduct.supplier?.name ?? '—'
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Unit</span>
                                <span>{{ store.currentProduct.unit }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground"
                                    >Created</span
                                >
                                <span>{{
                                    formatDate(store.currentProduct.created_at)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useProductStore } from '../stores/products';
import axios from 'axios';

const route = useRoute();
const store = useProductStore();

const showMovementForm = ref(false);
const moving = ref(false);
const movementError = ref('');
const movement = reactive({ type: 'in', quantity: 1, notes: '' });

const margin = computed(() => {
    if (!store.currentProduct || store.currentProduct.price === 0) return 0;
    return (
        ((store.currentProduct.price - store.currentProduct.cost) /
            store.currentProduct.price) *
        100
    ).toFixed(1);
});

onMounted(async () => {
    const id = route.params.id;
    if (id) await store.fetchProduct(Number(id));
});

async function recordMovement() {
    moving.value = true;
    movementError.value = '';
    const id = route.params.id;
    try {
        await axios.post(`/api/products/${id}/movements`, { ...movement });
        showMovementForm.value = false;
        movement.quantity = 1;
        movement.notes = '';
        if (id) await store.fetchProduct(Number(id));
    } catch (e: unknown) {
        if (e && typeof e === 'object' && 'response' in e) {
            const axiosError = e as { response: { data: { message: string } } };
            movementError.value =
                axiosError.response?.data?.message ??
                'Failed to record movement';
        }
    } finally {
        moving.value = false;
    }
}

function typeBadgeClass(type: string) {
    switch (type) {
        case 'in':
            return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        case 'out':
            return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
    }
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatNumber(n: number | string): string {
    return Number(n).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>
