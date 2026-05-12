import { Head, Link } from '@inertiajs/react';
import { Boxes, Package, ShoppingCart, Users } from 'lucide-react';
import { dashboard } from '@/routes';

export default function Dashboard() {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <Link href="/inventory" className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 hover:bg-accent/50 transition-colors group">
                        <Boxes className="mb-3 h-8 w-8 text-primary" />
                        <h3 className="text-lg font-semibold">Inventory Management</h3>
                        <p className="text-sm text-muted-foreground mt-1">Manage products, stock levels, and suppliers</p>
                    </Link>
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6">
                        <Package className="mb-3 h-8 w-8 text-muted-foreground" />
                        <h3 className="text-lg font-semibold">Orders</h3>
                        <p className="text-sm text-muted-foreground mt-1">Coming soon</p>
                    </div>
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6">
                        <Users className="mb-3 h-8 w-8 text-muted-foreground" />
                        <h3 className="text-lg font-semibold">Customers</h3>
                        <p className="text-sm text-muted-foreground mt-1">Coming soon</p>
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-6">
                    <h2 className="text-lg font-semibold mb-2">Quick Actions</h2>
                    <div className="flex flex-wrap gap-3">
                        <Link href="/inventory/products" className="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">
                            View Products
                        </Link>
                        <Link href="/inventory/categories" className="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm hover:bg-muted">
                            Manage Categories
                        </Link>
                        <Link href="/inventory/suppliers" className="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm hover:bg-muted">
                            Manage Suppliers
                        </Link>
                        <Link href="/inventory/reports" className="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm hover:bg-muted">
                            View Reports
                        </Link>
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
