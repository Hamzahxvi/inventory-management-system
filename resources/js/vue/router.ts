import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/inventory',
            name: 'dashboard',
            component: () => import('../vue/components/InventoryDashboard.vue'),
        },
        {
            path: '/inventory/products',
            name: 'products',
            component: () => import('../vue/components/ProductList.vue'),
        },
        {
            path: '/inventory/products/create',
            name: 'product-create',
            component: () => import('../vue/components/ProductForm.vue'),
        },
        {
            path: '/inventory/products/:id/edit',
            name: 'product-edit',
            component: () => import('../vue/components/ProductForm.vue'),
        },
        {
            path: '/inventory/products/:id',
            name: 'product-detail',
            component: () => import('../vue/components/ProductDetail.vue'),
        },
        {
            path: '/inventory/categories',
            name: 'categories',
            component: () => import('../vue/components/CategoryList.vue'),
        },
        {
            path: '/inventory/suppliers',
            name: 'suppliers',
            component: () => import('../vue/components/SupplierList.vue'),
        },
        {
            path: '/inventory/reports',
            name: 'reports',
            component: () => import('../vue/components/ReportsView.vue'),
        },
    ],
});

export default router;
