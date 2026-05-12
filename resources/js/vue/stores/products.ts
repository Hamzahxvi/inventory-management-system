import { defineStore } from 'pinia';
import axios from 'axios';
import type { Product, PaginatedResponse } from '@/types/inventory';

interface ProductFilters {
    search: string;
    category_id: string;
    supplier_id: string;
    low_stock: boolean;
    sort: string;
    direction: string;
    per_page: number;
}

interface ProductState {
    products: Product[];
    currentProduct: Product | null;
    loading: boolean;
    total: number;
    currentPage: number;
    lastPage: number;
    filters: ProductFilters;
}

export const useProductStore = defineStore('products', {
    state: (): ProductState => ({
        products: [],
        currentProduct: null,
        loading: false,
        total: 0,
        currentPage: 1,
        lastPage: 1,
        filters: {
            search: '',
            category_id: '',
            supplier_id: '',
            low_stock: false,
            sort: 'created_at',
            direction: 'desc',
            per_page: 15,
        },
    }),

    actions: {
        async fetchProducts(page = 1) {
            this.loading = true;
            try {
                const params: Record<string, string | number | boolean> = {
                    page,
                    per_page: this.filters.per_page,
                    sort: this.filters.sort,
                    direction: this.filters.direction,
                };
                if (this.filters.search) params.search = this.filters.search;
                if (this.filters.category_id) params.category_id = this.filters.category_id;
                if (this.filters.supplier_id) params.supplier_id = this.filters.supplier_id;
                if (this.filters.low_stock) params.low_stock = true;

                const { data } = await axios.get<PaginatedResponse<Product>>('/api/products', { params });
                this.products = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
                this.total = data.total;
            } finally {
                this.loading = false;
            }
        },

        async fetchProduct(id: number) {
            this.loading = true;
            try {
                const { data } = await axios.get<{ data: Product }>(`/api/products/${id}`);
                this.currentProduct = data.data;
            } finally {
                this.loading = false;
            }
        },

        async createProduct(product: Partial<Product>) {
            const { data } = await axios.post<{ data: Product }>('/api/products', product);
            return data.data;
        },

        async updateProduct(id: number, product: Partial<Product>) {
            const { data } = await axios.put<{ data: Product }>(`/api/products/${id}`, product);
            return data.data;
        },

        async deleteProduct(id: number) {
            await axios.delete(`/api/products/${id}`);
        },

        setFilters(filters: Partial<ProductFilters>) {
            Object.assign(this.filters, filters);
        },

        resetFilters() {
            this.filters = {
                search: '',
                category_id: '',
                supplier_id: '',
                low_stock: false,
                sort: 'created_at',
                direction: 'desc',
                per_page: 15,
            };
        },
    },
});
