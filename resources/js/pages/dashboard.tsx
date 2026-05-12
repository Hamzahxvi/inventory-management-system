import { Head } from '@inertiajs/react';
import { Boxes, Package, Users } from 'lucide-react';
import { dashboard } from '@/routes';

export default function Dashboard() {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <a
                        href="/inventory"
                        className="group relative overflow-hidden rounded-xl border border-sidebar-border/70 p-6 transition-colors hover:bg-accent/50 dark:border-sidebar-border"
                    >
                        <Boxes className="mb-3 h-8 w-8 text-primary" />
                        <h3 className="text-lg font-semibold">
                            Inventory Management
                        </h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Manage products, stock levels, and suppliers
                        </p>
                    </a>
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <Package className="mb-3 h-8 w-8 text-muted-foreground" />
                        <h3 className="text-lg font-semibold">Orders</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Coming soon
                        </p>
                    </div>
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <Users className="mb-3 h-8 w-8 text-muted-foreground" />
                        <h3 className="text-lg font-semibold">Customers</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Coming soon
                        </p>
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 p-6 md:min-h-min dark:border-sidebar-border">
                    <h2 className="mb-2 text-lg font-semibold">
                        Quick Actions
                    </h2>
                    <div className="flex flex-wrap gap-3">
                        <a
                            href="/inventory/products"
                            className="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                        >
                            View Products
                        </a>
                        <a
                            href="/inventory/categories"
                            className="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm hover:bg-muted"
                        >
                            Manage Categories
                        </a>
                        <a
                            href="/inventory/suppliers"
                            className="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm hover:bg-muted"
                        >
                            Manage Suppliers
                        </a>
                        <a
                            href="/inventory/reports"
                            className="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm hover:bg-muted"
                        >
                            View Reports
                        </a>
                    </div>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
