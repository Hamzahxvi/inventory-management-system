<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    private static array $skus = [];

    public function definition(): array
    {
        $sku = $this->uniqueSku();

        return [
            'sku' => $sku,
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence(),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'cost' => $this->faker->randomFloat(2, 0.5, 700),
            'stock_quantity' => $this->faker->numberBetween(0, 500),
            'low_stock_threshold' => $this->faker->numberBetween(5, 20),
            'unit' => $this->faker->randomElement(['pcs', 'kg', 'm', 'L', 'box', 'pack']),
        ];
    }

    private function uniqueSku(): string
    {
        do {
            $sku = strtoupper($this->faker->bothify('SKU-####-???'));
        } while (in_array($sku, self::$skus));

        self::$skus[] = $sku;

        return $sku;
    }
}
