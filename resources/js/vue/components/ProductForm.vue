<template>
    <div>
        <div class="mb-6">
            <router-link to="/inventory/products" class="text-sm text-muted-foreground hover:text-foreground">← Back to Products</router-link>
            <h2 class="mt-2 text-2xl font-bold">{{ isEdit ? 'Edit Product' : 'Add Product' }}</h2>
        </div>

        <form @submit.prevent="save" class="max-w-2xl space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">SKU *</label>
                    <input v-model="form.sku" type="text" required class="w-full rounded-md border px-3 py-2 text-sm" />
                    <p v-if="errors.sku" class="mt-1 text-xs text-destructive">{{ errors.sku[0] }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Name *</label>
                    <input v-model="form.name" type="text" required class="w-full rounded-md border px-3 py-2 text-sm" />
                    <p v-if="errors.name" class="mt-1 text-xs text-destructive">{{ errors.name[0] }}</p>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Description</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded-md border px-3 py-2 text-sm"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Category</label>
                    <select v-model="form.category_id" class="w-full rounded-md border px-3 py-2 text-sm">
                        <option :value="null">None</option>
                        <option v-for="cat in categoryStore.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Supplier</label>
                    <select v-model="form.supplier_id" class="w-full rounded-md border px-3 py-2 text-sm">
                        <option :value="null">None</option>
                        <option v-for="sup in supplierStore.suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Price *</label>
                    <input v-model="form.price" type="number" step="0.01" min="0" required class="w-full rounded-md border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Cost *</label>
                    <input v-model="form.cost" type="number" step="0.01" min="0" required class="w-full rounded-md border px-3 py-2 text-sm" />
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Stock Quantity *</label>
                    <input v-model="form.stock_quantity" type="number" min="0" required class="w-full rounded-md border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Low Stock Threshold *</label>
                    <input v-model="form.low_stock_threshold" type="number" min="0" required class="w-full rounded-md border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Unit *</label>
                    <select v-model="form.unit" class="w-full rounded-md border px-3 py-2 text-sm">
                        <option>pcs</option>
                        <option>kg</option>
                        <option>m</option>
                        <option>L</option>
                        <option>box</option>
                        <option>pack</option>
                    </select>
                </div>
            </div>

            <div v-if="isEdit" class="rounded-lg border border-destructive/20 bg-destructive/5 p-4">
                <span class="text-sm text-destructive">Adjusting stock here will create a stock movement record.</span>
            </div>

            <div class="flex justify-end gap-3 border-t pt-4">
                <router-link to="/inventory/products" class="rounded-md border px-4 py-2 text-sm hover:bg-muted">Cancel</router-link>
                <button type="submit" :disabled="saving" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50">
                    {{ saving ? 'Saving...' : isEdit ? 'Update Product' : 'Create Product' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useProductStore } from '../stores/products';
import { useCategoryStore } from '../stores/categories';
import { useSupplierStore } from '../stores/suppliers';

const route = useRoute();
const router = useRouter();
const store = useProductStore();
const categoryStore = useCategoryStore();
const supplierStore = useSupplierStore();

const isEdit = computed(() => !!route.params.id);
const saving = ref(false);
const errors = ref<Record<string, string[]>>({});

const form = reactive({
    sku: '',
    name: '',
    description: '',
    category_id: null as number | null,
    supplier_id: null as number | null,
    price: 0,
    cost: 0,
    stock_quantity: 0,
    low_stock_threshold: 10,
    unit: 'pcs',
});

onMounted(async () => {
    await Promise.all([categoryStore.fetchCategories(), supplierStore.fetchSuppliers()]);
    if (isEdit.value && route.params.id) {
        await store.fetchProduct(Number(route.params.id));
        if (store.currentProduct) {
            form.sku = store.currentProduct.sku;
            form.name = store.currentProduct.name;
            form.description = store.currentProduct.description ?? '';
            form.category_id = store.currentProduct.category_id;
            form.supplier_id = store.currentProduct.supplier_id;
            form.price = store.currentProduct.price;
            form.cost = store.currentProduct.cost;
            form.stock_quantity = store.currentProduct.stock_quantity;
            form.low_stock_threshold = store.currentProduct.low_stock_threshold;
            form.unit = store.currentProduct.unit;
        }
    }
});

async function save() {
    saving.value = true;
    errors.value = {};
    const id = route.params.id;
    try {
        const payload = { ...form };
        if (isEdit.value && id) {
            await store.updateProduct(Number(id), payload);
        } else {
            await store.createProduct(payload);
        }
        router.push('/inventory/products');
    } catch (e: unknown) {
        if (e && typeof e === 'object' && 'response' in e) {
            const axiosError = e as { response: { data: { errors: Record<string, string[]> } } };
            if (axiosError.response?.data?.errors) {
                errors.value = axiosError.response.data.errors;
            }
        }
    } finally {
        saving.value = false;
    }
}
</script>
