<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function csv(Request $request): Response
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
        }

        $products = $query->get();

        $csv = fopen('php://temp', 'r+');

        fputcsv($csv, [
            'SKU', 'Name', 'Description', 'Category', 'Supplier',
            'Price', 'Cost', 'Stock Qty', 'Low Stock Threshold',
            'Unit', 'Created At',
        ]);

        foreach ($products as $product) {
            fputcsv($csv, [
                $product->sku,
                $product->name,
                $product->description,
                $product->category?->name,
                $product->supplier?->name,
                $product->price,
                $product->cost,
                $product->stock_quantity,
                $product->low_stock_threshold,
                $product->unit,
                $product->created_at->toDateString(),
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_'.now()->format('Y-m-d').'.csv"',
        ]);
    }
}
