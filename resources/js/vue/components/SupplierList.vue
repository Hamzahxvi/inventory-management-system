<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Suppliers</h2>
                <p class="text-muted-foreground">Manage your suppliers</p>
            </div>
            <button @click="showForm = true; editing = null; resetForm()" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">
                Add Supplier
            </button>
        </div>

        <div v-if="store.loading" class="text-center py-12 text-muted-foreground">Loading...</div>

        <div v-else class="rounded-lg border">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Contact</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Phone</th>
                        <th class="px-4 py-2 text-right">Products</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="sup in store.suppliers" :key="sup.id" class="border-b hover:bg-muted/50">
                        <td class="px-4 py-2 font-medium">{{ sup.name }}</td>
                        <td class="px-4 py-2">{{ sup.contact_person ?? '—' }}</td>
                        <td class="px-4 py-2 text-muted-foreground">{{ sup.email ?? '—' }}</td>
                        <td class="px-4 py-2 text-muted-foreground">{{ sup.phone ?? '—' }}</td>
                        <td class="px-4 py-2 text-right">{{ sup.products_count }}</td>
                        <td class="px-4 py-2 text-right">
                            <button @click="editSupplier(sup)" class="rounded p-1 hover:bg-muted text-muted-foreground hover:text-foreground">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button @click="confirmDelete(sup)" class="rounded p-1 hover:bg-muted text-muted-foreground hover:text-destructive">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showForm = false">
            <div class="w-full max-w-md rounded-lg border bg-card p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-semibold">{{ editing ? 'Edit Supplier' : 'Add Supplier' }}</h3>
                <form @submit.prevent="saveSupplier" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Name *</label>
                        <input v-model="form.name" type="text" required class="w-full rounded-md border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Contact Person</label>
                        <input v-model="form.contact_person" type="text" class="w-full rounded-md border px-3 py-2 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Email</label>
                            <input v-model="form.email" type="email" class="w-full rounded-md border px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Phone</label>
                            <input v-model="form.phone" type="text" class="w-full rounded-md border px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Address</label>
                        <textarea v-model="form.address" rows="2" class="w-full rounded-md border px-3 py-2 text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showForm = false" class="rounded-md border px-4 py-2 text-sm hover:bg-muted">Cancel</button>
                        <button type="submit" :disabled="saving" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50">
                            {{ saving ? 'Saving...' : editing ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showDelete = false">
            <div class="w-full max-w-sm rounded-lg border bg-card p-6 shadow-lg">
                <h3 class="mb-2 text-lg font-semibold">Delete Supplier</h3>
                <p class="mb-4 text-sm text-muted-foreground">Are you sure you want to delete "{{ deleteTarget?.name }}"?</p>
                <div class="flex justify-end gap-2">
                    <button @click="showDelete = false" class="rounded-md border px-4 py-2 text-sm hover:bg-muted">Cancel</button>
                    <button @click="doDelete" class="rounded-md bg-destructive px-4 py-2 text-sm text-destructive-foreground hover:bg-destructive/90">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useSupplierStore } from '../stores/suppliers';
import type { Supplier } from '@/types/inventory';

const store = useSupplierStore();
const showForm = ref(false);
const showDelete = ref(false);
const editing = ref<Supplier | null>(null);
const deleteTarget = ref<Supplier | null>(null);
const saving = ref(false);

const form = reactive({
    name: '',
    contact_person: '',
    email: '',
    phone: '',
    address: '',
});

onMounted(() => store.fetchSuppliers());

function resetForm() {
    form.name = '';
    form.contact_person = '';
    form.email = '';
    form.phone = '';
    form.address = '';
}

function editSupplier(sup: Supplier) {
    editing.value = sup;
    form.name = sup.name;
    form.contact_person = sup.contact_person ?? '';
    form.email = sup.email ?? '';
    form.phone = sup.phone ?? '';
    form.address = sup.address ?? '';
    showForm.value = true;
}

async function saveSupplier() {
    saving.value = true;
    if (editing.value) {
        await store.updateSupplier(editing.value.id, { ...form });
    } else {
        await store.createSupplier({ ...form });
    }
    showForm.value = false;
    editing.value = null;
    resetForm();
    saving.value = false;
    store.fetchSuppliers();
}

function confirmDelete(sup: Supplier) {
    deleteTarget.value = sup;
    showDelete.value = true;
}

async function doDelete() {
    if (!deleteTarget.value) return;
    await store.deleteSupplier(deleteTarget.value.id);
    showDelete.value = false;
    store.fetchSuppliers();
}
</script>
