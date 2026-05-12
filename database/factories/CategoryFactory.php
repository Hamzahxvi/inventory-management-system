<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $categories = [
            'Electronics', 'Office Supplies', 'Furniture', 'Cleaning',
            'Packaging', 'Raw Materials', 'Tools', 'Safety Equipment',
        ];

        $name = $this->faker->unique()->randomElement($categories);

        return [
            'name' => $name,
            'description' => $this->faker->sentence(),
        ];
    }
}
