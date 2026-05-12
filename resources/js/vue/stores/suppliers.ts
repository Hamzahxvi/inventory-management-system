import axios from 'axios';
import { defineStore } from 'pinia';
import type { Supplier } from '@/types/inventory';

interface SupplierState {
    suppliers: Supplier[];
    loading: boolean;
}

export const useSupplierStore = defineStore('suppliers', {
    state: (): SupplierState => ({
        suppliers: [],
        loading: false,
    }),

    actions: {
        async fetchSuppliers() {
            this.loading = true;

            try {
                const { data } = await axios.get<{ data: Supplier[] }>(
                    '/api/suppliers',
                );
                this.suppliers = data.data;
            } finally {
                this.loading = false;
            }
        },

        async createSupplier(supplier: Partial<Supplier>) {
            const { data } = await axios.post<{ data: Supplier }>(
                '/api/suppliers',
                supplier,
            );

            return data.data;
        },

        async updateSupplier(id: number, supplier: Partial<Supplier>) {
            const { data } = await axios.put<{ data: Supplier }>(
                `/api/suppliers/${id}`,
                supplier,
            );

            return data.data;
        },

        async deleteSupplier(id: number) {
            await axios.delete(`/api/suppliers/${id}`);
        },
    },
});
