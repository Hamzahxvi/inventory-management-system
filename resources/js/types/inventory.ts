interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'manager' | 'staff';
}

interface Product {
    id: number;
    sku: string;
    name: string;
    description: string | null;
    category_id: number | null;
    category: Category | null;
    supplier_id: number | null;
    supplier: Supplier | null;
    price: number;
    cost: number;
    stock_quantity: number;
    low_stock_threshold: number;
    unit: string;
    is_low_stock: boolean;
    created_at: string;
    updated_at: string;
}

interface Category {
    id: number;
    name: string;
    description: string | null;
    products_count: number;
    created_at: string;
    updated_at: string;
}

interface Supplier {
    id: number;
    name: string;
    contact_person: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    products_count: number;
    created_at: string;
    updated_at: string;
}

interface StockMovement {
    id: number;
    product_id: number;
    product_name: string;
    user_id: number;
    user_name: string;
    type: 'in' | 'out' | 'adjustment';
    quantity: number;
    quantity_before: number;
    quantity_after: number;
    notes: string | null;
    created_at: string;
}

interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface InventorySummary {
    category: string;
    total_products: number;
    total_stock: number;
    inventory_value: number;
    inventory_cost: number;
    low_stock_count: number;
}

interface InventoryTotals {
    total_products: number;
    total_stock: number;
    total_value: number;
    low_stock_count: number;
}

interface ReportData {
    by_category: InventorySummary[];
    totals: InventoryTotals | null;
}

interface StockMovementReport {
    date: string;
    type: string;
    movement_count: number;
    total_quantity: number;
}

interface TopProductValue {
    id: number;
    sku: string;
    name: string;
    stock_quantity: number;
    price: number;
    total_value: number;
}

interface TopProductMovement {
    id: number;
    sku: string;
    name: string;
    stock_quantity: number;
    total_movements: number;
}

interface TopProductsReport {
    top_by_value: TopProductValue[];
    top_by_movement: TopProductMovement[];
}

export type {
    User,
    Product,
    Category,
    Supplier,
    StockMovement,
    PaginatedResponse,
    InventorySummary,
    InventoryTotals,
    ReportData,
    StockMovementReport,
    TopProductValue,
    TopProductMovement,
    TopProductsReport,
};
