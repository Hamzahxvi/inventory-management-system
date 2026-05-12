import axios from 'axios';
import { defineStore } from 'pinia';
import type { Category } from '@/types/inventory';

interface CategoryState {
    categories: Category[];
    loading: boolean;
}

export const useCategoryStore = defineStore('categories', {
    state: (): CategoryState => ({
        categories: [],
        loading: false,
    }),

    actions: {
        async fetchCategories() {
            this.loading = true;

            try {
                const { data } = await axios.get<{ data: Category[] }>(
                    '/api/categories',
                );
                this.categories = data.data;
            } finally {
                this.loading = false;
            }
        },

        async createCategory(category: Partial<Category>) {
            const { data } = await axios.post<{ data: Category }>(
                '/api/categories',
                category,
            );

            return data.data;
        },

        async updateCategory(id: number, category: Partial<Category>) {
            const { data } = await axios.put<{ data: Category }>(
                `/api/categories/${id}`,
                category,
            );

            return data.data;
        },

        async deleteCategory(id: number) {
            await axios.delete(`/api/categories/${id}`);
        },
    },
});
