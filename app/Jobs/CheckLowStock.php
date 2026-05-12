<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckLowStock implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->get();

        if ($lowStockProducts->isEmpty()) {
            Log::info('Low stock check: All products are well-stocked.');

            return;
        }

        $productList = $lowStockProducts->map(fn (Product $p) => "{$p->sku} - {$p->name} ({$p->stock_quantity} {$p->unit} remaining)")
            ->implode("\n");

        Log::warning("Low stock alert: {$lowStockProducts->count()} products are running low.\n{$productList}");
    }
}
