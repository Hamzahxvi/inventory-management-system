<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function inventorySummary(): JsonResponse
    {
        $summary = DB::select(<<<'SQL'
            SELECT
                c.name AS category,
                COUNT(p.id) AS total_products,
                COALESCE(SUM(p.stock_quantity), 0) AS total_stock,
                COALESCE(SUM(p.price * p.stock_quantity), 0) AS inventory_value,
                COALESCE(SUM(p.cost * p.stock_quantity), 0) AS inventory_cost,
                COUNT(CASE WHEN p.stock_quantity <= p.low_stock_threshold THEN 1 END) AS low_stock_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id AND p.deleted_at IS NULL
            GROUP BY c.id, c.name
            ORDER BY inventory_value DESC
        SQL);

        $totals = DB::select(<<<'SQL'
            SELECT
                COUNT(*) AS total_products,
                COALESCE(SUM(stock_quantity), 0) AS total_stock,
                COALESCE(SUM(price * stock_quantity), 0) AS total_value,
                COUNT(CASE WHEN stock_quantity <= low_stock_threshold THEN 1 END) AS low_stock_count
            FROM products
            WHERE deleted_at IS NULL
        SQL);

        return response()->json([
            'by_category' => $summary,
            'totals' => $totals[0] ?? null,
        ]);
    }

    public function stockMovementHistory(Request $request): JsonResponse
    {
        $request->validate(['days' => 'nullable|integer|min:1|max:365']);

        $days = $request->input('days', 30);

        $movements = DB::select(<<<'SQL'
            SELECT
                DATE(sm.created_at) AS date,
                sm.type,
                COUNT(*) AS movement_count,
                SUM(sm.quantity) AS total_quantity
            FROM stock_movements sm
            WHERE sm.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(sm.created_at), sm.type
            ORDER BY DATE(sm.created_at) DESC, sm.type
        SQL, [(int) $days]);

        return response()->json($movements);
    }

    public function topProducts(Request $request): JsonResponse
    {
        $request->validate(['limit' => 'nullable|integer|min:1|max:100']);

        $limit = (int) $request->input('limit', 10);

        $topByValue = DB::select(<<<'SQL'
            SELECT
                p.id,
                p.sku,
                p.name,
                p.stock_quantity,
                p.price,
                (p.price * p.stock_quantity) AS total_value
            FROM products p
            WHERE p.deleted_at IS NULL
            ORDER BY total_value DESC
            LIMIT ?
        SQL, [$limit]);

        $topByMovement = DB::select(<<<'SQL'
            SELECT
                p.id,
                p.sku,
                p.name,
                p.stock_quantity,
                COUNT(sm.id) AS total_movements
            FROM products p
            JOIN stock_movements sm ON sm.product_id = p.id
            WHERE p.deleted_at IS NULL
            GROUP BY p.id, p.sku, p.name, p.stock_quantity
            ORDER BY total_movements DESC
            LIMIT ?
        SQL, [$limit]);

        return response()->json([
            'top_by_value' => $topByValue,
            'top_by_movement' => $topByMovement,
        ]);
    }
}
