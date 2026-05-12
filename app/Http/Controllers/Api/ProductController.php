<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');
        $allowedSorts = ['name', 'sku', 'price', 'stock_quantity', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir);
        }

        return ProductResource::collection($query->paginate($request->input('per_page', 15)));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'type' => 'in',
            'quantity' => $product->stock_quantity,
            'quantity_before' => 0,
            'quantity_after' => $product->stock_quantity,
            'notes' => 'Initial stock',
        ]);

        return response()->json(new ProductResource($product->load(['category', 'supplier'])), 201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['category', 'supplier', 'stockMovements.user']));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $oldQuantity = $product->stock_quantity;
        $product->update($request->validated());

        if ((int) $request->input('stock_quantity') !== $oldQuantity) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'type' => 'adjustment',
                'quantity' => abs($product->stock_quantity - $oldQuantity),
                'quantity_before' => $oldQuantity,
                'quantity_after' => $product->stock_quantity,
                'notes' => $request->input('adjustment_notes', 'Stock adjustment'),
            ]);
        }

        return new ProductResource($product->load(['category', 'supplier']));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
