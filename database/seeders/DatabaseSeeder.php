<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'role' => 'manager',
        ]);

        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role' => 'staff',
        ]);

        $categories = Category::factory(8)->create();

        $suppliers = Supplier::factory(10)->create();

        foreach ($categories as $category) {
            Product::factory(5)->create([
                'category_id' => $category->id,
                'supplier_id' => $suppliers->random()->id,
            ]);
        }

        Product::factory(10)->create([
            'category_id' => $categories->random()->id,
            'supplier_id' => $suppliers->random()->id,
            'stock_quantity' => 0,
        ]);
    }
}
