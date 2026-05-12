# Inventory Management System

A full-stack inventory management application built with Laravel 13, Vue.js 3, and MySQL.

## Live Demo

*(Add your deployment URL here after deploying to Railway/Render/Laravel Forge)*

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 (PHP 8.3+) |
| Frontend (Auth) | React 19 + Inertia.js + Tailwind CSS v4 |
| Frontend (Inventory) | Vue.js 3 + Vue Router + Pinia + Axios |
| Database | MySQL |
| Queue | Database driver |
| Auth | Laravel Fortify (2FA, email verification) |

---

## Features

- Product CRUD with SKU generation, category/supplier assignment
- Stock movement tracking (in/out/adjustment) with full audit trail
- Role-based access control (Admin, Manager, Staff)
- Search, filter, and sort products
- Low stock alerts via background jobs
- CSV export for products
- Inventory analytics with raw SQL reports
- Dashboard with inventory summary and top products
- Category and Supplier management
- Responsive UI with dark mode support

---

## User Stories (Agile / Scrum)

### Sprint 1 — Foundation
- [x] As an admin, I want to create product categories so I can organize inventory
- [x] As an admin, I want to add suppliers so I can track where products come from
- [x] As an admin, I want to create products with SKU, price, cost, and stock quantity

### Sprint 2 — Inventory Operations
- [x] As a manager, I want to record stock movements (in/out/adjustment) so stock levels stay accurate
- [x] As a manager, I want to view stock movement history for any product
- [x] As a staff member, I want to search and filter products quickly

### Sprint 3 — Reports & Alerts
- [x] As an admin, I want to see inventory dashboards with total value and stock counts
- [x] As an admin, I want raw SQL reports on inventory by category and top products
- [x] As an admin, I want to receive low stock alerts via background jobs
- [x] As a manager, I want to export product data to CSV

### Sprint 4 — Security & Polish
- [x] As a system owner, I want role-based access so only admins/managers can modify data
- [x] As an admin, I want to view full audit trails on stock movements
- [x] As a user, I want to navigate between dashboard and inventory seamlessly

---

## Database Schema (Normalized)

```
users
  ├── id, name, email, password, role (admin|manager|staff)
  └── email_verified_at, timestamps

categories
  ├── id, name (unique), description
  └── timestamps

suppliers
  ├── id, name, contact_person, email, phone, address
  └── timestamps

products
  ├── id, sku (unique), name, description
  ├── category_id → categories (nullable)
  ├── supplier_id → suppliers (nullable)
  ├── price, cost, stock_quantity, low_stock_threshold, unit
  ├── timestamps, soft_deletes
  └── INDEX on category_id, supplier_id

stock_movements
  ├── id, product_id → products (cascade)
  ├── user_id → users (nullable)
  ├── type (in|out|adjustment)
  ├── quantity, quantity_before, quantity_after
  ├── reference_type, reference_id (polymorphic)
  ├── notes, timestamps
  └── INDEX on product_id, (reference_type, reference_id)
```

---

## Role-Based Access

| Action | Admin | Manager | Staff |
|---|---|---|---|
| View products | ✓ | ✓ | ✓ |
| Create/Edit/Delete products | ✓ | ✓ | |
| Record stock movements | ✓ | ✓ | ✓ |
| Manage categories | ✓ | ✓ | |
| Manage suppliers | ✓ | ✓ | |
| View reports | ✓ | ✓ | ✓ |
| Export CSV | ✓ | ✓ | ✓ |

---

## API Endpoints

All endpoints require authentication.

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/api/products` | auth | List/search products |
| POST | `/api/products` | admin,manager | Create product |
| GET | `/api/products/{id}` | auth | Show product details |
| PUT | `/api/products/{id}` | admin,manager | Update product |
| DELETE | `/api/products/{id}` | admin,manager | Delete product |
| POST | `/api/products/{id}/movements` | auth | Record stock movement |
| GET | `/api/categories` | auth | List categories |
| POST | `/api/categories` | admin,manager | Create category |
| PUT | `/api/categories/{id}` | admin,manager | Update category |
| DELETE | `/api/categories/{id}` | admin,manager | Delete category |
| GET | `/api/suppliers` | auth | List suppliers |
| POST | `/api/suppliers` | admin,manager | Create supplier |
| PUT | `/api/suppliers/{id}` | admin,manager | Update supplier |
| DELETE | `/api/suppliers/{id}` | admin,manager | Delete supplier |
| GET | `/api/movements` | auth | List stock movements |
| GET | `/api/reports/inventory-summary` | auth | Inventory by category |
| GET | `/api/reports/top-products` | auth | Top products report |
| GET | `/api/reports/stock-movements` | auth | Movement history |
| GET | `/api/export/csv` | auth | Export products to CSV |

---

## Raw SQL Reports

The `ReportController` uses raw SQL for analytics queries, demonstrating complex query skills beyond Eloquent:

- **Inventory Summary**: `LEFT JOIN` aggregation with conditional `CASE` counts
- **Stock Movement History**: Date-based `GROUP BY` with parameterized intervals
- **Top Products**: Subqueries for value ranking and movement frequency

---

## Background Jobs

- **CheckLowStock**: Dispatches via `php artisan inventory:check-low-stock`
  - Queries all products where `stock_quantity <= low_stock_threshold`
  - Logs low stock products (readily extendable to email notifications)
  - Run on a schedule via Laravel's task scheduler

---

## Setup Instructions

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 20+
- MySQL 8.0+

### Installation

```bash
# Clone the repository
git clone <repo-url>
cd example-app

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure your database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Generate app key
php artisan key:generate

# Run migrations and seed
php artisan migrate --seed

# Install Node dependencies
npm install

# Start development servers
composer run dev
```

### Default Users (after seeding)

| Email | Password | Role |
|---|---|---|
| admin@example.com | password | Admin |
| manager@example.com | password | Manager |
| staff@example.com | password | Staff |

### Starting the Queue Worker

```bash
php artisan queue:work
# or use the included dev command which starts queue automatically:
composer run dev
```

---

## Project Structure

```
app/
├── Console/Commands/
│   └── DispatchLowStockCheck.php    # Artisan command
├── Http/
│   ├── Controllers/Api/
│   │   ├── ProductController.php    # Product CRUD with search/filter
│   │   ├── CategoryController.php   # Category CRUD
│   │   ├── SupplierController.php   # Supplier CRUD
│   │   ├── StockMovementController.php # Stock in/out/adjustment
│   │   ├── ReportController.php     # Raw SQL analytics
│   │   └── ExportController.php     # CSV export
│   ├── Middleware/
│   │   └── RoleMiddleware.php       # Role-based access
│   ├── Requests/                    # Form validation
│   └── Resources/                   # API Resources (Product, Category, etc.)
├── Jobs/
│   └── CheckLowStock.php            # Low stock notification job
└── Models/
    ├── Product.php
    ├── Category.php
    ├── Supplier.php
    └── StockMovement.php

resources/js/
├── vue/                             # Vue.js SPA for inventory
│   ├── InventoryApp.vue             # Vue root component
│   ├── router.ts                    # Vue Router config
│   ├── stores/                      # Pinia state management
│   │   ├── products.ts              # Product store with Axios
│   │   ├── categories.ts            # Category store
│   │   └── suppliers.ts             # Supplier store
│   ├── layouts/
│   │   └── InventoryLayout.vue      # Sidebar + main layout
│   └── components/
│       ├── InventoryDashboard.vue   # Summary dashboard
│       ├── ProductList.vue          # Products table + search
│       ├── ProductForm.vue          # Create/Edit form
│       ├── ProductDetail.vue        # Detail + stock movements
│       ├── CategoryList.vue         # Category management
│       ├── SupplierList.vue         # Supplier management
│       └── ReportsView.vue          # Analytics & charts
└── pages/                           # React/Inertia pages (auth)
```

---

## Git Workflow

- `main` — Production-ready code
- `feature/*` — New feature branches
- `bugfix/*` — Bug fix branches

Commit messages follow conventional format: `type(scope): description`

---

## Deployment

Recommended platforms:
- **Backend**: Laravel Forge, Railway, or Render
- **Database**: PlanetScale, Railway MySQL, or AWS RDS
- **Queue**: `database` queue driver (included), or Redis for production

```bash
# Production build
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

---

## License

MIT
