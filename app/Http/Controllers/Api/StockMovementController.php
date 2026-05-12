<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockMovementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = StockMovement::with(['product', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return StockMovementResource::collection(
            $query->latest()->paginate($request->input('per_page', 25))
        );
    }

    public function store(StockMovementRequest $request, Product $product): JsonResponse
    {
        $quantityBefore = $product->stock_quantity;
        $quantity = $request->input('quantity');

        if ($request->input('type') === 'in') {
            $product->stock_quantity += $quantity;
        } elseif ($request->input('type') === 'out') {
            if ($product->stock_quantity < $quantity) {
                return response()->json(['message' => 'Insufficient stock'], 422);
            }
            $product->stock_quantity -= $quantity;
        } elseif ($request->input('type') === 'adjustment') {
            $product->stock_quantity = $quantity;
        }

        $product->save();

        $movement = StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'type' => $request->input('type'),
            'quantity' => abs($product->stock_quantity - $quantityBefore),
            'quantity_before' => $quantityBefore,
            'quantity_after' => $product->stock_quantity,
            'notes' => $request->input('notes'),
        ]);

        $movement->load(['product', 'user']);

        return response()->json(new StockMovementResource($movement), 201);
    }
}
